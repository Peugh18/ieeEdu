<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access admin courses list', function () {
    $response = $this->get(route('admin.courses.index'));
    $response->assertRedirect('/login');
});

test('regular users cannot access admin courses list', function () {
    $user = User::factory()->create(['role' => 'usuario']);

    $response = $this->actingAs($user)->get(route('admin.courses.index'));
    $response->assertForbidden();
});

test('admins can access admin courses list', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.courses.index'));
    $response->assertSuccessful();
});

test('admins can store a new course', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Finanzas']);

    $file = UploadedFile::fake()->create('course_cover.jpg', 100, 'image/jpeg');

    $payload = [
        'title' => 'Curso de Finanzas Públicas',
        'price' => 250,
        'type' => 'grabado',
        'category_id' => $category->id,
        'image_file' => $file,
        'description' => 'Un gran curso sobre finanzas públicas del país.',
        'instructor_name' => 'Dr. Alejandro Toledo',
        'instructor_title' => 'Especialista en Finanzas',
        'instructor_bio' => 'Alejandro tiene 10 años de experiencia.',
    ];

    $response = $this->actingAs($admin)->post(route('admin.courses.store'), $payload);

    $course = Course::first();
    expect($course)->not->toBeNull();
    expect($course->title)->toBe('Curso de Finanzas Públicas');
    expect($course->price)->toBe(250.0);
    expect($course->status)->toBe('BORRADOR');
    expect($course->category_id)->toBe($category->id);
    expect($course->image)->toContain('/storage/courses/');

    $response->assertRedirect(route('admin.courses.edit', $course->id));
});

test('admin can create lesson material with upload file', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::create([
        'title' => 'Curso de Prueba',
        'price' => 0,
        'type' => 'grabado',
        'status' => 'PUBLICADO',
    ]);
    $lesson = CourseLesson::create([
        'course_id' => $course->id,
        'title' => 'Lección de Prueba',
    ]);

    $file = UploadedFile::fake()->create('guia.pdf', 500, 'application/pdf');

    $response = $this->actingAs($admin)->postJson(route('admin.lessons.materials.store', $lesson->id), [
        'title' => 'Guía de Estudio',
        'file' => $file,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('course_materials', [
        'lesson_id' => $lesson->id,
        'title' => 'Guía de Estudio',
        'kind' => 'pdf',
    ]);
});
