<?php

namespace App\Services;

use App\Models\User;
use App\Models\CourseQuiz;
use App\Models\CourseExamAttempt;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use App\Services\CertificateService;

class ExamService
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Check if a user is allowed to take a quiz.
     */
    public function canTakeQuiz(User $user, CourseQuiz $quiz)
    {
        $attemptCount = CourseExamAttempt::where('user_id', $user->id)
            ->where('course_quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->count();

        return $attemptCount < $quiz->max_attempts;
    }

    /**
     * Handle the submission of a quiz attempt.
     */
    public function submit(User $user, CourseQuiz $quiz, array $answers)
    {
        // 1. Validaciones preventivas
        if (!$this->canTakeQuiz($user, $quiz)) {
            throw new \Exception('Has superado el límite de intentos permitido para esta evaluación.');
        }

        // Asegurar que el curso esté completado (Doble validación de seguridad)
        $progressService = app(\App\Services\ProgressService::class);
        $progress = $progressService->calculateCourseProgress($user, $quiz->course);
        if ($progress < 100) {
            throw new \Exception('Debes completar el 100% de las lecciones antes de rendir el examen.');
        }

        $quiz->load('questions.answers');
        
        $correctCount = 0;
        $totalQuestions = $quiz->questions->count();
        
        foreach($quiz->questions as $q) {
            if (isset($answers[$q->id])) {
                $selectedAns = $q->answers->where('id', $answers[$q->id])->first();
                if ($selectedAns && $selectedAns->is_correct) {
                    $correctCount++;
                }
            }
        }
        
        // Escala 0-20
        $finalScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 20) : 0;
        $minScore = $quiz->minimum_score ?? 14;
        $status = $finalScore >= $minScore ? 'aprobado' : 'reprobado';

        // Guardar el intento
        $attempt = CourseExamAttempt::create([
            'user_id' => $user->id,
            'course_quiz_id' => $quiz->id,
            'status' => $status,
            'score' => $finalScore,
            'answers_data' => $answers,
            'completed_at' => now(),
        ]);

        $certificateUrl = null;
        if ($status === 'aprobado') {
            $certificate = $this->handlePassing($user, $quiz);
            if ($certificate) {
                $certificateUrl = route('student.certificates.download', ['certificate' => $certificate->id]) . '?action=stream';
            }
        }

        return [
            'attempt_id' => $attempt->id,
            'score' => $finalScore,
            'status' => $status,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'passing_score' => $minScore,
            'certificate_url' => $certificateUrl,
            'message' => $status === 'aprobado' ? '¡Felicidades! Has aprobado la evaluación.' : 'Evaluación finalizada. Sigue practicando.'
        ];
    }

    /**
     * Actions to take when a user passes a quiz.
     */
    protected function handlePassing(User $user, CourseQuiz $quiz)
    {
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $quiz->course_id)
            ->first();
        
        $certificate = null;
        if ($enrollment) {
            $isEligible = $this->certificateService->checkEligibility($user, $quiz->course);
            if ($isEligible) {
                if (!$enrollment->completed_at) {
                    $enrollment->update(['completed_at' => now()]);
                }
                // Only create the RECORD, no PDF generation here!
                $certificate = $this->certificateService->getOrCreateRecord($user, $quiz->course);
            }
        }
        
        return $certificate;
    }
}
