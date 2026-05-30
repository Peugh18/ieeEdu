<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\PaymentRejectedNotification;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('aprueba pago y notifica', function () {
    Notification::fake();

    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();
    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
    ]);

    app(PaymentService::class)->approve($payment);

    Notification::assertSentTo(
        [$student], PaymentApprovedNotification::class
    );
});

test('rechaza pago y notifica', function () {
    Notification::fake();

    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();
    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'en_revision',
    ]);

    app(PaymentService::class)->reject($payment);

    Notification::assertSentTo(
        [$student], PaymentRejectedNotification::class
    );
});
