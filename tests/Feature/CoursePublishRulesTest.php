<?php

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\User;

test('publicar curso sin clases falla', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::factory()->create(['status' => 'BORRADOR', 'type' => 'grabado']);

    $response = $this->actingAs($admin)->patch(route('admin.courses.publish', $course));

    $response->assertSessionHasErrors();
    expect($course->fresh()->status)->toBe('BORRADOR');
});

test('publicar evento con 2 clases falla', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::factory()->create(['status' => 'BORRADOR', 'type' => 'evento']);
    $module = CourseModule::forceCreate(['course_id' => $course->id, 'title' => 'M1', 'sort_order' => 1]);
    CourseLesson::forceCreate(['course_id' => $course->id, 'module_id' => $module->id, 'title' => 'L1', 'sort_order' => 1, 'content_type' => 'video']);
    CourseLesson::forceCreate(['course_id' => $course->id, 'module_id' => $module->id, 'title' => 'L2', 'sort_order' => 2, 'content_type' => 'video']);

    $response = $this->actingAs($admin)->patch(route('admin.courses.publish', $course));

    $response->assertSessionHasErrors();
    expect($course->fresh()->status)->toBe('BORRADOR');
});

test('publicar grabado con 1 clase OK', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::factory()->create(['status' => 'BORRADOR', 'type' => 'grabado']);
    $module = CourseModule::forceCreate(['course_id' => $course->id, 'title' => 'M1', 'sort_order' => 1]);
    CourseLesson::forceCreate(['course_id' => $course->id, 'module_id' => $module->id, 'title' => 'L1', 'sort_order' => 1, 'content_type' => 'video']);

    $response = $this->actingAs($admin)->patch(route('admin.courses.publish', $course));

    $response->assertSessionHasNoErrors();
    expect($course->fresh()->status)->toBe('PUBLICADO');
});
