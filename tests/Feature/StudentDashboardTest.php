<?php

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseQuiz;
use App\Models\User;

test('student dashboard redirects guests to login', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('student can access dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);
});

test('student can access courses index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/student/courses');
    $response->assertStatus(200);
});

test('student can access live classes calendar', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/student/live-classes');
    $response->assertStatus(200);
});

test('student can access exams dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/student/exams');
    $response->assertStatus(200);
});

test('student can access certificates list', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/student/certificates');
    $response->assertStatus(200);
});

test('student can access profile settings', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/student/perfil');
    $response->assertStatus(200);
});

test('student can access explore catalog', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/student/explore/courses');
    $response->assertStatus(200);
});

test('student without enrollment cannot update classroom progress', function () {
    $user = User::factory()->create();
    $course = Course::create([
        'title' => 'Curso Pagado',
        'price' => 150,
        'type' => 'grabado',
        'status' => 'PUBLICADO',
    ]);
    $lesson = CourseLesson::create([
        'course_id' => $course->id,
        'title' => 'Lección 1',
    ]);

    $response = $this->actingAs($user)->post(route('student.classroom.progress'), [
        'lesson_id' => $lesson->id,
    ]);

    $response->assertStatus(403);
});

test('student without course access cannot submit exam', function () {
    $user = User::factory()->create();
    $course = Course::create([
        'title' => 'Curso Evaluado',
        'price' => 200,
        'type' => 'grabado',
        'status' => 'PUBLICADO',
    ]);
    $quiz = CourseQuiz::create([
        'course_id' => $course->id,
        'title' => 'Evaluación Final',
        'time_limit' => 30,
        'max_attempts' => 3,
        'minimum_score' => 14,
    ]);

    $response = $this->actingAs($user)->post(route('student.exams.submit', $quiz->id), [
        'answers' => [],
    ]);

    $response->assertStatus(403);
});
