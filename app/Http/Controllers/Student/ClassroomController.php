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

        // Centralized authorization through CoursePolicy
        $this->authorize('viewClassroom', $course);

        // Ensure enrollment exists for stats tracking (SAAS Logic)
        if ($user->hasSubscriptionActive() || $course->price <= 0) {
             Enrollment::firstOrCreate([
                 'user_id' => $user->id,
                 'course_id' => $course->id,
             ]);
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
            ->map(fn($id) => (int)$id)
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
            ->map(function(\App\Models\LessonComment $c) use ($user) {
                $c->is_liked = $c->isLikedBy($user);
                $c->replies->map(function(\App\Models\LessonComment $r) use ($user) {
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
            'lesson_id' => 'nullable|exists:course_lessons,id',
            'lesson_ids' => 'nullable|array',
            'lesson_ids.*' => 'exists:course_lessons,id'
        ]);

        $user = Auth::user();
        $lessonIds = $request->has('lesson_ids') ? $request->lesson_ids : [$request->lesson_id];
        
        $lastSyncProgress = 0;

        foreach ($lessonIds as $id) {
            if (!$id) continue;
            $lesson = CourseLesson::find($id);
            if (!$lesson) continue;

            \App\Models\LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'course_lesson_id' => $id],
                ['is_completed' => 1]
            );

            $progressService = app(\App\Services\ProgressService::class);
            $lastSyncProgress = $progressService->syncProgress($user, $lesson->course, $lesson->id);

            // Trigger certificate check/generation
            $this->certificateService->generateIfEligible($user, $lesson->course);
        }

        return $request->wantsJson() 
            ? response()->json(['success' => true, 'progress' => $lastSyncProgress]) 
            : back();
    }
}
