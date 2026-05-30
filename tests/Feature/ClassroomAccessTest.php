<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('estudiante con pago pendiente y sin enrollment no puede acceder al aula', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO']);

    // Create pending payment but no enrollment
    Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
    ]);

    $response = $this->actingAs($student)->get(route('student.classroom', $course->slug));

    $response->assertForbidden();
});

test('estudiante puede acceder al aula tras aprobacion del pago', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 100]);

    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
        'amount' => 100,
    ]);

    // Aprobar el pago como admin (lo cual debe crear el enrollment)
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->patch(route('admin.payments.approve', $payment))->assertRedirect();

    // Verify classroom access is now granted
    $response = $this->actingAs($student)->get(route('student.classroom', $course->slug));
    $response->assertSuccessful();
});
