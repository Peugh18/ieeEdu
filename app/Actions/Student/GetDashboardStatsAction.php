<?php

namespace App\Actions\Student;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\CourseLesson;
use App\Models\CourseQuiz;
use App\Models\CourseExamAttempt;
use App\Models\Certificate;

class GetDashboardStatsAction
{
    /**
     * Obtiene estadísticas consolidadas para el dashboard del estudiante.
     */
    public function execute(User $user): array
    {
        $enrollments = Enrollment::where('user_id', $user->id)->get();
        $enrolledIds = $enrollments->pluck('course_id');

        return [
            'active_courses'    => $enrollments->whereNull('completed_at')->count(),
            'completed_courses' => $enrollments->whereNotNull('completed_at')->count(),
            'upcoming_classes'  => CourseLesson::whereIn('course_id', $enrolledIds)
                ->where('start_time', '>', now())
                ->count(),
            'available_exams'   => CourseQuiz::whereIn('course_id', $enrolledIds)->count(),
            'average_score'     => round(CourseExamAttempt::where('user_id', $user->id)
                ->whereNotNull('score')
                ->avg('score') ?? 0, 1),
            'certificate_count' => Certificate::where('user_id', $user->id)->count(),
        ];
    }
}
