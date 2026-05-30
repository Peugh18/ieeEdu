<?php

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseQuiz;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\ProgressService;

test('progreso al 100 marca completed_at si no hay quiz', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();
    $lesson = CourseLesson::forceCreate(['course_id' => $course->id, 'title' => 'T', 'sort_order' => 1]);

    $enrollment = Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'progress' => 0,
        'completed_at' => null,
    ]);

    LessonProgress::create([
        'user_id' => $student->id,
        'course_lesson_id' => $lesson->id,
        'is_completed' => true,
    ]);

    $service = new ProgressService;
    $service->syncProgress($student, $course);

    $enrollment->refresh();
    expect($enrollment->completed_at)->not->toBeNull();
});

test('progreso al 100 NO marca completed_at si hay quiz final', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();
    $lesson = CourseLesson::forceCreate(['course_id' => $course->id, 'title' => 'T', 'sort_order' => 1]);
    CourseQuiz::factory()->create(['course_id' => $course->id]);

    $enrollment = Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'progress' => 0,
        'completed_at' => null,
    ]);

    LessonProgress::create([
        'user_id' => $student->id,
        'course_lesson_id' => $lesson->id,
        'is_completed' => true,
    ]);

    $service = new ProgressService;
    $service->syncProgress($student, $course);

    $enrollment->refresh();
    expect($enrollment->completed_at)->toBeNull();
});
