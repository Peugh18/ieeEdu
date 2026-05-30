<?php

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;

test('estudiante autenticado puede registrar solicitud de membresía por whatsapp', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $response = $this->actingAs($student)->post(route('student.purchase-intent.store'), [
        'plan' => 'semestral',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('open_whatsapp');

    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'course_id' => null,
        'subscription_type' => 'semestral',
        'status' => 'pendiente',
    ]);
});

test('plan invalido falla con error de validación', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $response = $this->actingAs($student)->post(route('student.purchase-intent.store'), [
        'plan' => 'mensual_desconocido',
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

test('guest es redirigido al intentar solicitud de membresía', function () {
    $response = $this->post(route('student.purchase-intent.store'), [
        'plan' => 'trimestral',
    ]);

    $response->assertRedirect(route('login'));
});

test('estudiante autenticado puede ver la página de estado de membresía', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $response = $this->actingAs($student)->get(route('student.subscriptions.payment.status'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('student/SubscriptionPaymentStatus'));
});

test('estudiante puede subir comprobante de membresía pendiente en mis pagos', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $payment = Payment::create([
        'user_id' => $student->id,
        'subscription_type' => 'trimestral',
        'amount' => 350,
        'status' => 'pendiente',
    ]);

    $file = UploadedFile::fake()->create('comprobante.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($student)->post(route('student.payments.comprobante', $payment), [
        'comprobante' => $file,
    ]);

    $response->assertRedirect();
    $payment->refresh();
    expect($payment->status)->toBe('en_revision');
    expect($payment->comprobante)->not->toBeNull();
});
