<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;

test('regular users cannot view or manage payments', function () {
    $user = User::factory()->create(['role' => 'usuario']);
    $payment = Payment::factory()->create();

    $this->actingAs($user)->get(route('admin.payments.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.payments.show', $payment->id))->assertForbidden();
    $this->actingAs($user)->post(route('admin.payments.store'), [])->assertForbidden();
    $this->actingAs($user)->patch(route('admin.payments.approve', $payment->id))->assertForbidden();
    $this->actingAs($user)->patch(route('admin.payments.reject', $payment->id))->assertForbidden();
});

test('admins can view and manage payments', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $payment = Payment::factory()->create(['status' => 'pendiente']);

    $this->actingAs($admin)->get(route('admin.payments.index'))->assertSuccessful();
    $this->actingAs($admin)->get(route('admin.payments.show', $payment->id))->assertSuccessful();

    $this->actingAs($admin)->patch(route('admin.payments.approve', $payment->id))->assertRedirect();
    expect($payment->fresh()->status)->toBe('aprobado');
});

test('payment store request validates unique active enrollment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();

    // Crear un enrollment
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'subscription_granted' => false,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.payments.store'), [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'amount' => 100,
        'status' => 'pendiente',
    ]);

    $response->assertSessionHasErrors(['course_id']);
});
