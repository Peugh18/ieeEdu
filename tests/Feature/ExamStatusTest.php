<?php

use App\Models\Course;
use App\Models\CourseQuiz;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('exam status is bloqueado when progress is less than 100', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['status' => 'PUBLICADO']);
    $quiz = CourseQuiz::factory()->create(['course_id' => $course->id, 'max_attempts' => 2]);

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'progress' => 50,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('student.exams.index'));
    $response->assertSuccessful();

    $exams = $response->viewData('page')['props']['blockedExams'];
    expect($exams)->toHaveCount(1)
        ->and($exams[0]['status'])->toBe('bloqueado');
});

test('exam status is disponible when progress is 100 and no attempts made', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['status' => 'PUBLICADO']);
    $quiz = CourseQuiz::factory()->create(['course_id' => $course->id, 'max_attempts' => 2]);

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'progress' => 100,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('student.exams.index'));
    $response->assertSuccessful();

    $exams = $response->viewData('page')['props']['availableExams'];
    expect($exams)->toHaveCount(1)
        ->and($exams[0]['status'])->toBe('disponible');
});
