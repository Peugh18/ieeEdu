<?php

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;

it('approves payment without crashing when course_id is null', function () {
    $user = User::factory()->create();

    // Crear pago sin curso (membresía)
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'course_id' => null,
        'status' => 'en_revision',
    ]);

    $service = app(PaymentService::class);

    // Al aprobar no debe fallar por intentar inscribir en un curso null
    $approvedPayment = $service->approve($payment);

    expect($approvedPayment->status)->toBe('aprobado');
    $this->assertDatabaseMissing('enrollments', ['payment_id' => $payment->id]);
});
