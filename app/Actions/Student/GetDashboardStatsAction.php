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
     *
     * Regla: los cursos otorgados por suscripción (subscription_granted=true)
     * solo se cuentan como "activos" si la suscripción sigue activa
     * (subscription_active=true). Nunca se ocultan los cursos comprados
     * individualmente (subscription_granted=false).
     */
    public function execute(User $user): array
    {
        $visibleEnrollments = Enrollment::where('user_id', $user->id)
            ->visible()
            ->get();

        $visibleCourseIds = $visibleEnrollments->pluck('course_id');

        return [
            'active_courses'    => $visibleEnrollments->whereNull('completed_at')->count(),
            'completed_courses' => $visibleEnrollments->whereNotNull('completed_at')->count(),
            'upcoming_classes'  => CourseLesson::whereIn('course_id', $visibleCourseIds)
                ->where('start_time', '>', now())
                ->count(),
            'available_exams'   => CourseQuiz::whereIn('course_id', $visibleCourseIds)->count(),
            'average_score'     => round(CourseExamAttempt::where('user_id', $user->id)
                ->whereNotNull('score')
                ->avg('score') ?? 0, 1),
            'certificate_count' => Certificate::where('user_id', $user->id)->count(),
        ];
    }
}

