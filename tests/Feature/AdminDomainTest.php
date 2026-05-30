<?php

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;

test('admin cannot publish course without lessons', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::create([
        'title' => 'Borrador sin clases',
        'price' => 100,
        'type' => 'grabado',
        'status' => 'BORRADOR',
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.courses.publish', $course));

    $response->assertRedirect();
    $response->assertSessionHasErrors('course');
    expect($course->fresh()->status)->toBe('BORRADOR');
});

test('admin can publish course with at least one lesson', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::create([
        'title' => 'Curso listo',
        'price' => 100,
        'type' => 'grabado',
        'status' => 'BORRADOR',
    ]);
    CourseLesson::create([
        'course_id' => $course->id,
        'title' => 'Clase 1',
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.courses.publish', $course));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($course->fresh()->status)->toBe('PUBLICADO');
});

test('admin cannot publish evento with more than one lesson', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::create([
        'title' => 'Masterclass inválida',
        'price' => 50,
        'type' => 'evento',
        'status' => 'BORRADOR',
    ]);
    CourseLesson::create(['course_id' => $course->id, 'title' => 'Clase 1']);
    CourseLesson::create(['course_id' => $course->id, 'title' => 'Clase 2']);

    $response = $this->actingAs($admin)->patch(route('admin.courses.publish', $course));

    $response->assertRedirect();
    $response->assertSessionHasErrors('course');
    expect($course->fresh()->status)->toBe('BORRADOR');
});

test('approving payment enrolls student in course', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO']);
    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
    ]);

    expect(
        Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists()
    )->toBeFalse();

    $this->actingAs($admin)->patch(route('admin.payments.approve', $payment))->assertRedirect();

    expect($payment->fresh()->status)->toBe('aprobado');
    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'payment_id' => $payment->id,
    ]);
});

test('rejecting payment does not enroll student', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO']);
    $payment = Payment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
    ]);

    $this->actingAs($admin)->patch(route('admin.payments.reject', $payment))->assertRedirect();

    expect($payment->fresh()->status)->toBe('rechazado');
    expect(
        Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists()
    )->toBeFalse();
});

test('creating subscription grants access to published courses', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $courses = Course::factory()->count(2)->create(['status' => 'PUBLICADO', 'type' => 'grabado']);

    $this->actingAs($admin)->post(route('admin.subscriptions.store'), [
        'user_id' => $student->id,
        'type' => 'trimestral',
        'months' => 3,
        'amount' => 99,
    ])->assertRedirect();

    foreach ($courses as $course) {
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'subscription_granted' => true,
            'subscription_active' => true,
        ]);
    }

    expect(
        Subscription::where('user_id', $student->id)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->exists()
    )->toBeTrue();
});

test('invalid subscription type is rejected', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);

    $this->actingAs($admin)->post(route('admin.subscriptions.store'), [
        'user_id' => $student->id,
        'type' => 'mensual',
        'months' => 1,
        'amount' => 50,
    ])->assertSessionHasErrors('type');
});

test('cancelling subscription revokes subscription access', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO']);

    $this->actingAs($admin)->post(route('admin.subscriptions.store'), [
        'user_id' => $student->id,
        'type' => 'trimestral',
        'months' => 1,
        'amount' => 50,
    ]);

    $subscription = Subscription::where('user_id', $student->id)->firstOrFail();

    $this->actingAs($admin)->patch(route('admin.subscriptions.toggle', $subscription))->assertRedirect();

    $enrollment = Enrollment::where('user_id', $student->id)
        ->where('course_id', $course->id)
        ->first();

    expect($enrollment)->not->toBeNull();
    expect($enrollment->subscription_active)->toBeFalse();
});
