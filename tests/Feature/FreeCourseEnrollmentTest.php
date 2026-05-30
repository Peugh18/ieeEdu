<?php

use App\Models\Course;
use App\Models\User;

it('allows a user to enroll in a free course', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $course = Course::factory()->create(['price' => 0, 'sale_price' => 0]);

    $this->actingAs($user)
        ->post(route('student.courses.enroll-free', $course->slug))
        ->assertRedirect(route('student.classroom', $course->slug));

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'subscription_granted' => false,
    ]);
});

it('blocks enrolling for free if course is not free', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $course = Course::factory()->create(['price' => 100, 'sale_price' => 50]);

    $this->actingAs($user)
        ->post(route('student.courses.enroll-free', $course->slug))
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});
