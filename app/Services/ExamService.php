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
        if (!$this->canTakeQuiz($user, $quiz)) {
            throw new \Exception('Has superado el límite de intentos permitido para esta evaluación.');
        }

        $quiz->load('questions.answers');
        
        $scoreRaw = 0;
        foreach($quiz->questions as $q) {
            if (isset($answers[$q->id])) {
                $selectedAns = $q->answers->where('id', $answers[$q->id])->first();
                if ($selectedAns && $selectedAns->is_correct) {
                    $scoreRaw += 1;
                }
            }
        }
        
        $totalQuestions = $quiz->questions->count();
        $finalScore = $totalQuestions > 0 ? round(($scoreRaw / $totalQuestions) * 20) : 0;
        $status = $finalScore >= ($quiz->minimum_score ?? 14) ? 'aprobado' : 'reprobado';

        $attempt = CourseExamAttempt::where('user_id', $user->id)
            ->where('course_quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if (!$attempt) {
            $attempt = new CourseExamAttempt([
                'user_id' => $user->id,
                'course_quiz_id' => $quiz->id,
            ]);
        }

        $attempt->update([
            'status' => $status,
            'score' => $finalScore,
            'answers_data' => $answers,
            'completed_at' => now(),
        ]);

        if ($status === 'aprobado') {
            $this->handlePassing($user, $quiz);
        }

        return $attempt;
    }

    /**
     * Actions to take when a user passes a quiz.
     */
    protected function handlePassing(User $user, CourseQuiz $quiz)
    {
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $quiz->course_id)
            ->first();
        
        if ($enrollment) {
            $isEligible = $this->certificateService->checkEligibility($user, $quiz->course);
            if ($isEligible) {
                if (!$enrollment->completed_at) {
                    $enrollment->update(['completed_at' => now()]);
                }
            }
        }
        
        // Final attempt to generate certificate if everything is ready
        $this->certificateService->generateIfEligible($user, $quiz->course);
    }
}
