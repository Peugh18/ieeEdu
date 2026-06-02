<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CommentLike;
use App\Models\Course;
use App\Models\CourseExamAttempt;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\LessonComment;
use App\Models\LessonProgress;
use App\Models\Payment;
use App\Services\CertificateService;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function show(Course $course, ?CourseLesson $lesson = null)
    {
        $user = Auth::user();

        // Centralized authorization through CoursePolicy
        $this->authorize('viewClassroom', $course);

        // H1: IDOR fix — lesson must belong to the requested course
        if ($lesson && $lesson->course_id !== $course->id) {
            abort(404);
        }

        // Registrar inscripción para métricas, diferenciando Premium vs gratis vs compra individual
        $this->ensureEnrollmentForTracking($user, $course);

        // Cargar módulos y lecciones
        $course->load(['modules.lessons.materials', 'quizzes', 'certificateTemplate']);

        // Si no se especifica lección, tomar la primera
        if (! $lesson) {
            $lesson = $course->modules->first()?->lessons->first()
                      ?? $course->lessons->first();
        }

        // Si aún no hay lección (curso vacío)
        if (! $lesson) {
            return Inertia::render('student/Classroom', [
                'course' => $course,
                'currentLesson' => null,
                'prevLessonId' => null,
                'nextLessonId' => null,
            ]);
        }

        // Encontrar lección previa y siguiente para la navegación
        $allLessons = $course->modules->flatMap->lessons->concat($course->lessons->whereNull('module_id'));
        $currentIndex = $allLessons->search(fn ($l) => $l->id === $lesson->id);

        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('course_lesson_id', $allLessons->pluck('id'))
            ->where('is_completed', true)
            ->pluck('course_lesson_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        // Determinar si todos los videos están completados
        // Se define un curso completado si todas sus lecciones registradas en sistema están en $completedLessons
        $allLessonsCompleted = count($completedLessons) === $allLessons->count() && $allLessons->count() > 0;

        // Get Comments with likes and replies
        $comments = LessonComment::where('course_lesson_id', $lesson->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->get();

        // Optimización N+1: recopilar IDs de comentarios y respuestas
        $commentIds = $comments->pluck('id');
        $replyIds = $comments->flatMap(fn ($c) => $c->replies->pluck('id'));
        $allIds = $commentIds->concat($replyIds)->filter()->unique()->toArray();

        $likedCommentIds = [];
        if ($user && ! empty($allIds)) {
            $likedCommentIds = CommentLike::where('user_id', $user->id)
                ->whereIn('lesson_comment_id', $allIds)
                ->pluck('lesson_comment_id')
                ->toArray();
            $likedCommentIds = array_flip($likedCommentIds);
        }

        $comments->map(function (LessonComment $c) use ($likedCommentIds) {
            $c->is_liked = isset($likedCommentIds[$c->id]);
            $c->replies->map(function (LessonComment $r) use ($likedCommentIds) {
                $r->is_liked = isset($likedCommentIds[$r->id]);

                return $r;
            });

            return $c;
        });

        $quiz = $course->quizzes->first();
        $quizStats = null;
        if ($quiz) {
            $attempts = CourseExamAttempt::where('user_id', $user->id)
                ->where('course_quiz_id', $quiz->id)
                ->whereNotNull('completed_at');

            $passed = $attempts->clone()->where('status', 'aprobado')->exists();
            $attemptsLeft = max(0, $quiz->max_attempts - $attempts->count());

            $certificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            $quizStats = [
                'quiz_id' => $quiz->id,
                'passed' => $passed,
                'attempts_left' => $attemptsLeft,
                'max_attempts' => $quiz->max_attempts,
                'certificate_url' => $certificate ? route('student.certificates.download', ['certificate' => $certificate->id]).'?action=stream' : null,
                'certificate_code' => $certificate?->code,
                'certificate_date' => $certificate?->issue_date?->format('d/m/Y'),
            ];
        }

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
            'quizStats' => $quizStats,
        ]);
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'lesson_id' => 'nullable|exists:course_lessons,id',
            'lesson_ids' => 'nullable|array',
            'lesson_ids.*' => 'exists:course_lessons,id',
        ]);

        $user = Auth::user();
        $lessonIds = $request->has('lesson_ids') ? $request->lesson_ids : [$request->lesson_id];

        $lastSyncProgress = 0;

        foreach ($lessonIds as $id) {
            if (! $id) {
                continue;
            }
            $lesson = CourseLesson::find($id);
            if (! $lesson) {
                continue;
            }

            $this->authorize('viewClassroom', $lesson->course);

            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'course_lesson_id' => $id],
                ['is_completed' => 1]
            );

            $progressService = app(ProgressService::class);
            $lastSyncProgress = $progressService->syncProgress($user, $lesson->course, $lesson->id);

            // Trigger certificate check/generation
            $this->certificateService->generateIfEligible($user, $lesson->course);
        }

        return $request->wantsJson()
            ? response()->json(['success' => true, 'progress' => $lastSyncProgress])
            : back();
    }

    /**
     * Crea o actualiza inscripción para seguimiento de progreso.
     * Premium → subscription_granted=true (revocable al expirar).
     * Gratis → subscription_granted=false (permanente).
     * Compra individual → la crea PaymentService::approve(), no se toca aquí.
     */
    protected function ensureEnrollmentForTracking($user, Course $course): void
    {
        $price = $course->sale_price > 0 ? $course->sale_price : $course->price;

        $purchasedIndividually = Payment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'aprobado')
            ->exists();

        if ($purchasedIndividually) {
            return;
        }

        if ($course->retainsAccessAfterSubscriptionEnds()) {
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'subscription_granted' => false,
                ]
            );

            Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('subscription_granted', true)
                ->update([
                    'subscription_granted' => false,
                    'subscription_active' => true,
                ]);

            return;
        }

        if ($user->hasSubscriptionActive()) {
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'subscription_granted' => true,
                    'subscription_active' => true,
                ]
            );

            Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('subscription_granted', true)
                ->update(['subscription_active' => true]);

            return;
        }

        if ($price <= 0) {
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'subscription_granted' => false,
                ]
            );
        }
    }
}
