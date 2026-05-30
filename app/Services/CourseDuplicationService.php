<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Support\Str;

class CourseDuplicationService
{
    public function duplicate(Course $course): Course
    {
        $course->load([
            'modules.lessons.materials',
            'lessons.materials',
            'quizzes.questions.answers',
            'certificateTemplate',
        ]);

        $newCourse = $course->replicate();
        $newCourse->title = $course->title.' (Copia)';
        $newCourse->slug = $this->uniqueSlug($newCourse->title);
        $newCourse->status = 'BORRADOR';
        $newCourse->save();

        foreach ($course->modules as $module) {
            $newModule = $module->replicate();
            $newModule->course_id = $newCourse->id;
            $newModule->save();

            foreach ($module->lessons as $lesson) {
                $this->cloneLesson($lesson, $newCourse->id, $newModule->id);
            }
        }

        foreach ($course->lessons->whereNull('module_id') as $lesson) {
            $this->cloneLesson($lesson, $newCourse->id, null);
        }

        foreach ($course->quizzes as $quiz) {
            $newQuiz = $quiz->replicate();
            $newQuiz->course_id = $newCourse->id;
            $newQuiz->save();

            foreach ($quiz->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->quiz_id = $newQuiz->id;
                $newQuestion->save();

                foreach ($question->answers as $answer) {
                    $newAnswer = $answer->replicate();
                    $newAnswer->question_id = $newQuestion->id;
                    $newAnswer->save();
                }
            }
        }

        if ($course->certificateTemplate) {
            $newTemplate = $course->certificateTemplate->replicate();
            $newTemplate->course_id = $newCourse->id;
            $newTemplate->save();
        }

        return $newCourse;
    }

    private function cloneLesson(CourseLesson $lesson, int $courseId, ?int $moduleId): void
    {
        $newLesson = $lesson->replicate();
        $newLesson->course_id = $courseId;
        $newLesson->module_id = $moduleId;
        $newLesson->save();

        foreach ($lesson->materials as $material) {
            $newMaterial = $material->replicate();
            $newMaterial->lesson_id = $newLesson->id;
            $newMaterial->save();
        }
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $i = 1;

        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        return $slug;
    }
}
