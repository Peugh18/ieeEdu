<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\User;

test('premium classroom visit creates subscription_granted enrollment not permanent', function () {
    $user = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 200, 'type' => 'grabado']);

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'trimestral',
        'status' => Subscription::STATUS_ACTIVE,
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
    ]);

    $this->actingAs($user)
        ->get(route('student.classroom', $course->slug))
        ->assertSuccessful();

    $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();

    expect($enrollment)->not->toBeNull();
    expect($enrollment->subscription_granted)->toBeTrue();
    expect($enrollment->subscription_active)->toBeTrue();
});

test('after premium expires subscription_granted enrollment no longer grants access', function () {
    $user = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['status' => 'PUBLICADO', 'price' => 200, 'type' => 'grabado']);

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'trimestral',
        'status' => Subscription::STATUS_EXPIRED,
        'start_date' => now()->subMonths(4),
        'end_date' => now()->subDay(),
    ]);

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'subscription_granted' => true,
        'subscription_active' => false,
    ]);

    expect($user->hasAccess($course->id))->toBeFalse();
});
