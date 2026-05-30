<?php

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;

test('estudiante autenticado puede registrar un pago de membresía', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $file = UploadedFile::fake()->create('comprobante.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($student)->post(route('student.subscriptions.payment.store'), [
        'plan' => 'semestral',
        'comprobante' => $file,
    ]);

    $response->assertRedirect(route('student.subscriptions.payment.status'));

    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'course_id' => null,
        'subscription_type' => 'semestral',
        'status' => 'en_revision',
    ]);
});

test('plan invalido falla con error de validación', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $file = UploadedFile::fake()->create('comprobante.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($student)->post(route('student.subscriptions.payment.store'), [
        'plan' => 'mensual_desconocido',
        'comprobante' => $file,
    ]);

    $response->assertSessionHasErrors('plan');
});

test('al aprobar pago de suscripcion se crea la subscripcion y no llama enroll con course null', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => null,
        'subscription_type' => 'anual',
        'amount' => 990,
        'status' => 'en_revision',
        'comprobante' => 'comprobantes/test.jpg',
    ]);

    $service = app(PaymentService::class);
    $service->approve($payment);

    $payment->refresh();
    expect($payment->status)->toBe('aprobado');

    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $student->id,
        'type' => 'anual',
        'status' => 'activa',
    ]);
});

test('guest es redirigido al intentar pago de suscripcion', function () {
    $file = UploadedFile::fake()->create('comprobante.jpg', 100, 'image/jpeg');

    $response = $this->post(route('student.subscriptions.payment.store'), [
        'plan' => 'trimestral',
        'comprobante' => $file,
    ]);

    $response->assertRedirect(route('login'));
});
