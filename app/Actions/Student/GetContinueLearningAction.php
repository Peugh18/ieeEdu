<?php

namespace App\Actions\Student;

use App\Models\User;
use App\Models\LessonProgress;
use App\Models\Enrollment;
use App\Services\ProgressService;

class GetContinueLearningAction
{
    public function __construct(private ProgressService $progressService) {}

    /**
     * Determina la última lección vista y calcula el contexto de aprendizaje continuo.
     */
    public function execute(User $user): ?array
    {
        $lastLessonProgress = LessonProgress::where('user_id', $user->id)
            ->with(['lesson.module.course.category', 'lesson.course.category'])
            ->latest('updated_at')
            ->first();

        if (!$lastLessonProgress || !$lastLessonProgress->lesson) {
            return null;
        }

        $lesson = $lastLessonProgress->lesson;
        $course = $lesson->course ?? $lesson->module?->course;

        if (!$course) {
            return null;
        }

        // Verificar si el usuario aún tiene acceso (matrícula activa)
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || $enrollment->completed_at) {
            return null;
        }

        return [
            'course_title' => $course->title,
            'course_slug'  => $course->slug,
            'lesson_title' => $lesson->title,
            'lesson_id'    => $lesson->id,
            'progress'     => $this->progressService->calculateCourseProgress($user, $course),
            'image'        => $course->image,
        ];
    }
}
