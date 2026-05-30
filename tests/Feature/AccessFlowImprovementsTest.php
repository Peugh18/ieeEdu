<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;

test('premium user can register payment for permanent course access', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 200, 'type' => 'grabado']);

    Subscription::create([
        'user_id' => $student->id,
        'type' => 'trimestral',
        'status' => Subscription::STATUS_ACTIVE,
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
    ]);

    $file = UploadedFile::fake()->create('comprobante.jpg', 100, 'image/jpeg');

    $this->actingAs($student)
        ->post(route('student.payments.store'), [
            'course_id' => $course->id,
            'comprobante' => $file,
        ])
        ->assertRedirect(route('student.payments.index'));

    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'amount' => 200,
        'status' => 'en_revision',
    ]);
});

test('approving individual payment after premium sets permanent enrollment', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 200]);

    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'subscription_granted' => true,
        'subscription_active' => true,
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'amount' => 200,
        'status' => 'en_revision',
    ]);

    app(PaymentService::class)->approve($payment);

    $enrollment = Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->first();

    expect($enrollment->subscription_granted)->toBeFalse();
    expect($enrollment->payment_id)->toBe($payment->id);
    expect($student->fresh()->hasPermanentCourseAccess($course->id))->toBeTrue();
});

test('cannot approve already approved payment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $payment = Payment::factory()->create(['status' => 'aprobado']);

    $this->actingAs($admin)
        ->patch(route('admin.payments.approve', $payment))
        ->assertSessionHasErrors();
});

test('course update ignores status from editor', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::factory()->create(['status' => 'BORRADOR']);

    $this->actingAs($admin)
        ->put(route('admin.courses.update', $course), [
            'title' => $course->title,
            'price' => $course->price,
            'type' => 'grabado',
            'status' => 'PUBLICADO',
        ])
        ->assertRedirect();

    expect($course->fresh()->status)->toBe('BORRADOR');
});
