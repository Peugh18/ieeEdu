<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    /**
     * Calcula el porcentaje de progreso de un usuario en un curso específico.
     */
    public function calculateCourseProgress(User $user, Course $course): int
    {
        // Consulta directa a la base de datos para obtener todos los IDs de lecciones vinculadas
        $allLessonIds = CourseLesson::where('course_id', $course->id)
            ->orWhereIn('module_id', function ($query) use ($course) {
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
     * Calcula el progreso de varios cursos en una sola consulta para un usuario.
     */
    public function getBatchProgress(User $user, array $courseIds): array
    {
        if (empty($courseIds)) {
            return [];
        }

        // Get lesson counts including lessons in modules
        $lessonsQuery = DB::table('course_lessons')
            ->leftJoin('course_modules', 'course_lessons.module_id', '=', 'course_modules.id')
            ->select(
                DB::raw('COALESCE(course_lessons.course_id, course_modules.course_id) as course_id'),
                DB::raw('count(course_lessons.id) as count')
            )
            ->whereIn(DB::raw('COALESCE(course_lessons.course_id, course_modules.course_id)'), $courseIds)
            ->groupBy(DB::raw('COALESCE(course_lessons.course_id, course_modules.course_id)'));

        $lessonCounts = $lessonsQuery->pluck('count', 'course_id')->all();

        // Get completed counts
        $completedCounts = DB::table('lesson_progress')
            ->where('lesson_progress.user_id', $user->id)
            ->where('lesson_progress.is_completed', 1)
            ->join('course_lessons', 'lesson_progress.course_lesson_id', '=', 'course_lessons.id')
            ->leftJoin('course_modules', 'course_lessons.module_id', '=', 'course_modules.id')
            ->select(
                DB::raw('COALESCE(course_lessons.course_id, course_modules.course_id) as course_id'),
                DB::raw('count(lesson_progress.id) as count')
            )
            ->whereIn(DB::raw('COALESCE(course_lessons.course_id, course_modules.course_id)'), $courseIds)
            ->groupBy(DB::raw('COALESCE(course_lessons.course_id, course_modules.course_id)'))
            ->pluck('count', 'course_id')
            ->all();

        $progressMap = [];
        foreach ($courseIds as $courseId) {
            $total = $lessonCounts[$courseId] ?? 0;
            $completed = $completedCounts[$courseId] ?? 0;
            $progressMap[$courseId] = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
        }

        return $progressMap;
    }

    /**
     * Sincroniza y guarda el progreso en la tabla enrollments.
     */
    public function syncProgress(User $user, Course $course, ?int $lastLessonId = null): int
    {
        $progress = $this->calculateCourseProgress($user, $course);

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $data = ['progress' => $progress];
            if ($lastLessonId) {
                $data['last_lesson_id'] = $lastLessonId;
            }

            // Si el progreso es 100, marcamos como completado (solo si no hay examen final)
            if ($progress >= 100 && ! $enrollment->completed_at) {
                if (! $course->hasFinalQuiz()) {
                    $data['completed_at'] = now();
                }
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
