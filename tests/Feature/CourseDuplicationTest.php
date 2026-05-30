<?php

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\CourseQuiz;
use App\Models\User;
use App\Services\CourseDuplicationService;

test('duplicate course clones modules lessons and quiz', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $course = Course::factory()->create(['title' => 'Curso Original', 'status' => 'PUBLICADO']);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Módulo 1',
        'description' => 'Desc',
        'sort_order' => 1,
    ]);

    CourseLesson::create([
        'course_id' => $course->id,
        'module_id' => $module->id,
        'title' => 'Lección A',
        'content_type' => 'video',
        'video_url' => 'https://example.com/v',
        'sort_order' => 1,
    ]);

    CourseLesson::create([
        'course_id' => $course->id,
        'module_id' => null,
        'title' => 'Lección suelta',
        'content_type' => 'video',
        'video_url' => 'https://example.com/v2',
        'sort_order' => 2,
    ]);

    CourseQuiz::factory()->create(['course_id' => $course->id, 'title' => 'Examen final']);

    $duplicate = app(CourseDuplicationService::class)->duplicate($course->fresh());

    expect($duplicate->id)->not->toBe($course->id)
        ->and($duplicate->status)->toBe('BORRADOR')
        ->and($duplicate->modules)->toHaveCount(1)
        ->and($duplicate->lessons)->toHaveCount(2)
        ->and($duplicate->quizzes)->toHaveCount(1);
});
