<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CourseExamAttempt;
use App\Models\CourseQuiz;
use App\Models\Enrollment;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExamController extends Controller
{
    public function __construct(
        protected ExamService $examService
    ) {}

    /**
     * Sección de Exámenes
     */
    public function exams()
    {
        $user = Auth::user();
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)->visible()->pluck('course_id')->toArray();

        $quizzes = CourseQuiz::whereIn('course_id', $enrolledCourseIds)
            ->with([
                'course.enrollments' => fn ($q) => $q->where('user_id', $user->id),
                'attempts' => fn ($q) => $q->where('user_id', $user->id)->whereNotNull('completed_at')->orderBy('created_at', 'desc'),
            ])
            ->get()
            ->map(function (CourseQuiz $quiz) {
                $attempts = $quiz->attempts;
                $passed = $attempts->contains('status', 'aprobado');
                $latestAttempt = $attempts->first();
                $enrollment = $quiz->course?->enrollments->first();

                $attemptsLeft = max(0, $quiz->max_attempts - $attempts->count());
                $progress = (int) ($enrollment?->progress ?? 0);

                if ($progress < 100) {
                    $status = 'bloqueado';
                } elseif ($passed) {
                    $status = 'aprobado';
                } elseif ($attemptsLeft === 0) {
                    $status = 'reprobado';
                } else {
                    $status = 'disponible';
                }

                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'course_title' => $quiz->course->title ?? '',
                    'time_limit' => $quiz->time_limit,
                    'attempts_left' => $attemptsLeft,
                    'passed' => $passed,
                    'progress' => (int) ($enrollment?->progress ?? 0),
                    'date' => $latestAttempt?->completed_at?->format('d M Y') ?? 'Pendiente',
                    'score' => (int) ($latestAttempt?->score ?? 0),
                    'passing_score' => (int) ($quiz->minimum_score ?? config('education.passing_score', 14)),
                    'status' => $status,
                ];
            });

        $availableExams = $quizzes->where('status', 'disponible')->values();
        $blockedExams = $quizzes->where('status', 'bloqueado')->values();

        return Inertia::render('student/Exams', [
            'availableExams' => $availableExams,
            'blockedExams' => $blockedExams,
            'history' => $this->getExamHistory($user),
            'stats' => [
                'average_score' => round(CourseExamAttempt::where('user_id', $user->id)->whereNotNull('score')->avg('score') ?? 0, 1),
                'certificate_count' => Certificate::where('user_id', $user->id)->count(),
            ],
        ]);
    }

    /**
     * Iniciar un examen
     */
    public function takeExam(CourseQuiz $quiz)
    {
        $user = Auth::user();

        // 1. Verificar acceso al curso
        $this->authorize('viewClassroom', $quiz->course);

        // 2. Verificar progreso (Seguridad adicional)
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $quiz->course_id)
            ->visible()
            ->first();
        if (! $enrollment || $enrollment->progress < 100) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Debes completar el 100% del curso para rendir la evaluación final.');
        }

        // 3. Verificar intentos permitidos
        if (! $this->examService->canTakeQuiz($user, $quiz)) {
            return redirect()->route('student.classroom', ['course' => $quiz->course->slug])
                ->with('error', 'Has superado el límite de intentos permitido para esta evaluación.');
        }

        // 4. Cargar preguntas con sus alternativas
        return Inertia::render('student/TakeExam', [
            'quiz' => $quiz->load(['questions.answers', 'course']),
            'current_attempt' => CourseExamAttempt::where('user_id', $user->id)->where('course_quiz_id', $quiz->id)->count() + 1,
        ]);
    }

    /**
     * Enviar examen
     */
    public function submitExam(Request $request, CourseQuiz $quiz)
    {
        $user = Auth::user();

        // Verificar acceso al curso
        $this->authorize('viewClassroom', $quiz->course);

        try {
            $result = $this->examService->submit($user, $quiz, $request->answers);

            return back()->with('exam_result', $result);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function getExamHistory($user)
    {
        return CourseExamAttempt::where('user_id', $user->id)
            ->with(['quiz.course'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->quiz->title ?? '',
                'course_title' => $a->quiz->course->title ?? '',
                'date' => $a->completed_at ? $a->completed_at->format('d M Y') : 'En progreso',
                'score' => $a->score,
                'passing_score' => $a->quiz->minimum_score ?? config('education.passing_score'),
                'status' => $a->status,
            ]);
    }
}
