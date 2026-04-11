<?php

namespace App\Actions\Student;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class GetCourseRecommendationsAction
{
    /**
     * Obtiene recomendaciones de cursos basadas en las categorías inscritas del usuario.
     */
    public function execute(User $user, int $limit = 3): \Illuminate\Support\Collection
    {
        $enrolledIds = Enrollment::where('user_id', $user->id)->pluck('course_id');
        $enrolledCategories = Enrollment::where('user_id', $user->id)
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->pluck('courses.category_id')
            ->unique();
        
        // 1. Cursos de la misma categoría (mejor recomendación)
        $recommendations = Course::whereNotIn('id', $enrolledIds)
            ->where('status', 'PUBLICADO')
            ->whereIn('category_id', $enrolledCategories)
            ->with('category')
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->take($limit)
            ->get();

        // 2. Si faltan, rellenar con cursos más vendidos generales
        if ($recommendations->count() < $limit) {
            $moreRecs = Course::whereNotIn('id', $enrolledIds)
                ->whereNotIn('id', $recommendations->pluck('id'))
                ->where('status', 'PUBLICADO')
                ->with('category')
                ->withCount('enrollments')
                ->orderByDesc('enrollments_count')
                ->latest()
                ->take($limit - $recommendations->count())
                ->get();
            
            $recommendations = $recommendations->concat($moreRecs);
        }

        return $recommendations;
    }
}
