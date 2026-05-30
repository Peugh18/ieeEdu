<?php

use App\Models\Course;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Support\PlanPricing;
use Illuminate\Http\UploadedFile;

test('plan pricing returns configured or default price', function () {
    expect(PlanPricing::price('trimestral'))->toBe(350.0);
    expect(PlanPricing::price('semestral'))->toBe(600.0);
    expect(PlanPricing::price('anual'))->toBe(990.0);
});

test('student subscription purchase intent stores plan price', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $this->actingAs($student)->post(route('student.purchase-intent.store'), [
        'plan' => 'trimestral',
    ])->assertRedirect()->assertSessionHas('open_whatsapp');

    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'subscription_type' => 'trimestral',
        'amount' => 350,
        'status' => 'pendiente',
    ]);
});

test('admin subscriptions index shows membership payment amount', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);

    Subscription::create([
        'user_id' => $student->id,
        'type' => 'trimestral',
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
        'status' => Subscription::STATUS_ACTIVE,
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => null,
        'subscription_type' => 'trimestral',
        'amount' => 350,
        'status' => 'aprobado',
        'comprobante' => 'comprobantes/test.jpg',
    ]);

    // Pago de curso no debe pisar el monto de membresía
    $course = Course::factory()->create();
    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'amount' => 99,
        'status' => 'aprobado',
        'comprobante' => 'comprobantes/curso.jpg',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.subscriptions.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/Subscriptions')
        ->has('subscriptions.data', 1)
        ->where('subscriptions.data.0.payment_amount', 350)
    );
});
