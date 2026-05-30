<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('estudiante puede descargar su certificado', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['certificate_enabled' => true]);
    $certificate = Certificate::forceCreate([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'file_path' => 'certs/1.pdf',
        'code' => 'CERT-123',
        'issue_date' => now(),
    ]);

    Storage::fake('public');
    Storage::disk('public')->put('certs/1.pdf', 'cert content');

    $lesson = CourseLesson::forceCreate(['course_id' => $course->id, 'title' => 'T', 'sort_order' => 1]);
    LessonProgress::create([
        'user_id' => $student->id,
        'course_lesson_id' => $lesson->id,
        'is_completed' => true,
    ]);

    $response = $this->actingAs($student)->get(route('student.certificates.download', $certificate));
    $response->assertSuccessful();
});

test('estudiante NO puede descargar certificado ajeno', function () {
    $studentA = User::factory()->create(['role' => 'usuario']);
    $studentB = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();
    $certificate = Certificate::forceCreate([
        'user_id' => $studentB->id,
        'course_id' => $course->id,
        'file_path' => 'certs/2.pdf',
        'code' => 'CERT-456',
        'issue_date' => now(),
    ]);

    $response = $this->actingAs($studentA)->get(route('student.certificates.download', $certificate));
    $response->assertForbidden();
});

test('estudiante NO puede descargar certificado si no es elegible', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['certificate_enabled' => true]);
    $certificate = Certificate::forceCreate([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'file_path' => 'certs/3.pdf',
        'code' => 'CERT-789',
        'issue_date' => now(),
    ]);

    $lesson = CourseLesson::forceCreate(['course_id' => $course->id, 'title' => 'T', 'sort_order' => 1]);
    // Sin LessonProgress, por lo que no es elegible

    $response = $this->actingAs($student)->get(route('student.certificates.download', $certificate));
    $response->assertForbidden();
});
