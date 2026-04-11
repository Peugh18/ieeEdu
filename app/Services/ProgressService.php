<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\LessonProgress;
use App\Models\CourseLesson;

class ProgressService
{
    /**
     * Calcula el porcentaje de progreso de un usuario en un curso específico.
     */
    public function calculateCourseProgress(User $user, Course $course): int
    {
        // Consulta directa a la base de datos para obtener todos los IDs de lecciones vinculadas
        $allLessonIds = CourseLesson::where('course_id', $course->id)
            ->orWhereIn('module_id', function($query) use ($course) {
                $query->select('id')->from('course_modules')->where('course_id', $course->id);
            })
            ->pluck('id')
            ->unique()
            ->toArray();

        $allLessonsCount = count($allLessonIds);

        if ($allLessonsCount === 0) {
            return 0;
        }

        // Contamos el progreso real basado en esos IDs exactos
        $completedCount = LessonProgress::where('user_id', $user->id)
            ->whereIn('course_lesson_id', $allLessonIds)
            ->where('is_completed', 1)
            ->count();

        return (int) round(($completedCount / $allLessonsCount) * 100);
    }

    /**
     * Sincroniza y guarda el progreso en la tabla enrollments.
     */
    public function syncProgress(User $user, Course $course, int $lastLessonId = null): int
    {
        $progress = $this->calculateCourseProgress($user, $course);
        
        $enrollment = \App\Models\Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $data = ['progress' => $progress];
            if ($lastLessonId) {
                $data['last_lesson_id'] = $lastLessonId;
            }
            
            // Si el progreso es 100, marcamos como completado
            if ($progress >= 100 && !$enrollment->completed_at) {
                $data['completed_at'] = now();
            }

            $enrollment->update($data);
        }

        return $progress;
    }

    /**
     * Obtiene los IDs de las lecciones completadas por el usuario en un curso.
     */
    public function getCompletedLessonIds(User $user, Course $course): array
    {
        $allLessonIds = $course->modules->flatMap->lessons->pluck('id')
            ->concat($course->lessons->whereNull('module_id')->pluck('id'))
            ->unique()
            ->toArray();

        return LessonProgress::where('user_id', $user->id)
            ->whereIn('course_lesson_id', $allLessonIds)
            ->where('is_completed', 1)
            ->pluck('course_lesson_id')
            ->toArray();
    }
}
