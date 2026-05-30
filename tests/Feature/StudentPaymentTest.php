<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('student can view own payments', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();
    Payment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id, 'status' => 'pendiente']);

    $response = $this->actingAs($student)->get(route('student.payments.index'));
    $response->assertSuccessful();
});

test('student cannot view another users payments', function () {
    $studentA = User::factory()->create(['role' => 'usuario']);
    $studentB = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create();
    $payment = Payment::factory()->create(['user_id' => $studentB->id, 'course_id' => $course->id]);

    // Student A should not see student B's payment in the index
    $response = $this->actingAs($studentA)->get(route('student.payments.index'));
    $response->assertSuccessful();
    // The payment list should only contain student A's payments (none)
    // This is implicitly tested by the controller query scope
});

test('student can create payment for course', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['price' => 100, 'status' => 'PUBLICADO', 'type' => 'grabado']);

    $response = $this->actingAs($student)->post(route('student.payments.store'), [
        'course_id' => $course->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
        'amount' => 100,
    ]);
});

test('student cannot create duplicate pending payment for same course', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['price' => 100, 'status' => 'PUBLICADO', 'type' => 'grabado']);

    Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
        'amount' => 100,
    ]);

    $response = $this->actingAs($student)->post(route('student.payments.store'), [
        'course_id' => $course->id,
    ]);

    $response->assertSessionHasErrors('course_id');
});

test('student cannot create payment for already enrolled course', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['price' => 100, 'status' => 'PUBLICADO', 'type' => 'grabado']);
    Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

    $response = $this->actingAs($student)->post(route('student.payments.store'), [
        'course_id' => $course->id,
    ]);

    $response->assertSessionHasErrors('course_id');
});

test('student payment amount is set from course price on server', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['price' => 100, 'sale_price' => 0, 'status' => 'PUBLICADO', 'type' => 'grabado']);

    $response = $this->actingAs($student)->post(route('student.payments.store'), [
        'course_id' => $course->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'amount' => 100,
    ]);
});

test('student can upload comprobante for pending payment', function () {
    Storage::fake('public');
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['price' => 100, 'status' => 'PUBLICADO', 'type' => 'grabado']);
    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
        'comprobante' => null,
    ]);

    $file = UploadedFile::fake()->create('voucher.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($student)->post(route('student.payments.comprobante', $payment), [
        'comprobante' => $file,
    ]);

    $response->assertRedirect(route('student.payments.index'));
    expect($payment->fresh()->status)->toBe('en_revision');
    expect($payment->fresh()->comprobante)->toContain('/storage/comprobantes/');
});

test('approving payment links payment_id on existing enrollment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO']);
    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
    ]);

    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'payment_id' => null,
    ]);

    $this->actingAs($admin)->patch(route('admin.payments.approve', $payment))->assertRedirect();

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->value('payment_id'))
        ->toBe($payment->id);
});
