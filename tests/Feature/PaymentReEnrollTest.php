<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

it('allows submitting a payment for a course when previous access expired', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['price' => 100, 'status' => 'PUBLICADO', 'type' => 'grabado']);

    // Usuario tenía enrollment por suscripción, pero ya expiró (subscription_active = false)
    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'subscription_granted' => true,
        'subscription_active' => false,
    ]);

    $this->actingAs($user)
        ->post(route('student.payments.store'), [
            'course_id' => $course->id,
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
    ]);
});

it('blocks submitting a payment if user has permanent access', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['price' => 100, 'status' => 'PUBLICADO', 'type' => 'grabado']);

    // Usuario ya compró individualmente
    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'subscription_granted' => false,
    ]);

    $this->actingAs($user)
        ->post(route('student.payments.store'), [
            'course_id' => $course->id,
        ])
        ->assertSessionHasErrors(['course_id']);
});
