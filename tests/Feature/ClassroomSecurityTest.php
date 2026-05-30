<?php

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\User;

test('student cannot access lesson from another course via idor', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $courseA = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 0]);
    $courseB = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 0]);

    $lessonA = CourseLesson::create(['course_id' => $courseA->id, 'title' => 'Clase A']);
    $lessonB = CourseLesson::create(['course_id' => $courseB->id, 'title' => 'Clase B']);

    // Enroll student in course A
    Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $courseA->id]);

    // IDOR attempt: access course A with lesson B
    $response = $this->actingAs($student)->get(route('student.classroom', [
        'course' => $courseA->slug,
        'lesson' => $lessonB->id,
    ]));

    $response->assertNotFound();
});

test('student can access valid lesson within enrolled course', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 0]);
    $lesson = CourseLesson::create(['course_id' => $course->id, 'title' => 'Clase 1']);

    Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

    $response = $this->actingAs($student)->get(route('student.classroom', [
        'course' => $course->slug,
        'lesson' => $lesson->id,
    ]));

    $response->assertSuccessful();
});
