<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;

test('admin can revert approved course payment and revoke enrollment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO']);
    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'aprobado',
    ]);

    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'payment_id' => $payment->id,
        'subscription_granted' => false,
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.payments.revert', $payment));

    $response->assertRedirect();
    expect($payment->fresh()->status)->toBe('en_revision');
    expect(
        Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists()
    )->toBeFalse();
});

test('cannot revert non-approved payment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $payment = Payment::factory()->create(['status' => 'pendiente']);

    $this->actingAs($admin)
        ->patch(route('admin.payments.revert', $payment))
        ->assertForbidden();

    expect($payment->fresh()->status)->toBe('pendiente');
});

test('non-admin cannot revert payment', function () {
    $user = User::factory()->create(['role' => 'usuario']);
    $payment = Payment::factory()->create(['status' => 'aprobado']);

    $this->actingAs($user)
        ->patch(route('admin.payments.revert', $payment))
        ->assertForbidden();
});
