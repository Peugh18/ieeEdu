<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\CertificateService;

class ClassroomController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }
    public function show(Course $course, CourseLesson $lesson = null)
    {
        $user = Auth::user();

        // Verificar inscripción
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('cursos.show', $course->slug)
                ->with('error', 'No estás inscrito en este curso.');
        }

        // Cargar módulos y lecciones
        $course->load(['modules.lessons.materials', 'quizzes']);

        // Si no se especifica lección, tomar la primera
        if (!$lesson) {
            $lesson = $course->modules->first()?->lessons->first() 
                      ?? $course->lessons->first();
        }

        // Si aún no hay lección (curso vacío)
        if (!$lesson) {
            return Inertia::render('student/Classroom', [
                'course' => $course,
                'currentLesson' => null,
                'prevLessonId' => null,
                'nextLessonId' => null,
            ]);
        }

        // Encontrar lección previa y siguiente para la navegación
        $allLessons = $course->modules->flatMap->lessons->concat($course->lessons->whereNull('module_id'));
        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        $completedLessons = \App\Models\LessonProgress::where('user_id', $user->id)
            ->whereIn('course_lesson_id', $allLessons->pluck('id'))
            ->where('is_completed', true)
            ->pluck('course_lesson_id')
            ->toArray();

        // Determinar si todos los videos están completados
        // Se define un curso completado si todas sus lecciones registradas en sistema están en $completedLessons
        $allLessonsCompleted = count($completedLessons) === $allLessons->count() && $allLessons->count() > 0;

        // Get Comments with likes and replies
        $comments = \App\Models\LessonComment::where('course_lesson_id', $lesson->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($c) use ($user) {
                $c->is_liked = $c->isLikedBy($user);
                $c->replies->map(function($r) use ($user) {
                    $r->is_liked = $r->isLikedBy($user);
                    return $r;
                });
                return $c;
            });

        return Inertia::render('student/Classroom', [
            'course' => $course,
            'currentLesson' => $lesson->load('materials'),
            'prevLessonId' => $prevLesson?->id,
            'nextLessonId' => $nextLesson?->id,
            'allLessonsCount' => $allLessons->count(),
            'currentLessonIndex' => $currentIndex + 1,
            'completedLessons' => $completedLessons,
            'allLessonsCompleted' => $allLessonsCompleted,
            'comments' => $comments,
        ]);
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:course_lessons,id'
        ]);

        $user = Auth::user();
        $lesson = \App\Models\CourseLesson::find($request->lesson_id);

        $progress = \App\Models\LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'course_lesson_id' => $request->lesson_id],
            ['is_completed' => true]
        );

        // Update Enrollment Table Progress
        if ($lesson) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $lesson->course_id)
                ->first();

            if ($enrollment) {
                // Determine course structure (modules vs flat lessons)
                $course = $lesson->course;
                $allLessonsCount = \App\Models\CourseLesson::where('course_id', $course->id)->count();
                
                $completedCount = \App\Models\LessonProgress::where('user_id', $user->id)
                    ->whereHas('lesson', function($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->where('is_completed', true)
                    ->count();

                $progressPercent = $allLessonsCount > 0 ? round(($completedCount / $allLessonsCount) * 100) : 0;
                
                // Get eligibility status (videos + exams)
                $isEligible = $this->certificateService->checkEligibility($user, $course);
                
                $enrollment->update([
                    'progress' => $progressPercent,
                    'last_lesson_id' => $lesson->id,
                    'completed_at' => $isEligible ? ($enrollment->completed_at ?? now()) : null,
                ]);
            }

            // Generate certificate if full criteria met
            $this->certificateService->generateIfEligible($user, $lesson->course);
        }

        return response()->json(['status' => 'success']);
    }
}
