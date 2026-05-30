<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionAccessService;

test('masterclass enrollment persists after subscription is removed', function () {
    $user = User::factory()->create(['role' => 'usuario']);
    $masterclass = Course::factory()->create([
        'status' => 'PUBLICADO',
        'type' => 'evento',
        'price' => 0,
    ]);
    $paidCourse = Course::factory()->create([
        'status' => 'PUBLICADO',
        'type' => 'grabado',
        'price' => 200,
    ]);

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'trimestral',
        'status' => Subscription::STATUS_ACTIVE,
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
    ]);

    app(SubscriptionAccessService::class)->sync($user->id);

    $this->actingAs($user)
        ->get(route('student.classroom', $masterclass->slug))
        ->assertSuccessful();

    expect(
        Enrollment::where('user_id', $user->id)
            ->where('course_id', $masterclass->id)
            ->where('subscription_granted', false)
            ->exists()
    )->toBeTrue();

    Subscription::where('user_id', $user->id)->update([
        'status' => Subscription::STATUS_CANCELLED,
    ]);

    app(SubscriptionAccessService::class)->sync($user->id);

    expect($user->fresh()->hasAccess($masterclass->id))->toBeTrue();
    expect($user->fresh()->hasAccess($paidCourse->id))->toBeFalse();

    expect(
        Enrollment::where('user_id', $user->id)
            ->where('course_id', $masterclass->id)
            ->visible()
            ->exists()
    )->toBeTrue();
});

test('revoke converts legacy premium masterclass enrollment to permanent', function () {
    $user = User::factory()->create(['role' => 'usuario']);
    $masterclass = Course::factory()->create([
        'status' => 'PUBLICADO',
        'type' => 'evento',
        'price' => 0,
    ]);

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $masterclass->id,
        'subscription_granted' => true,
        'subscription_active' => true,
    ]);

    app(SubscriptionAccessService::class)->revokeAccess($user->id);

    $enrollment = Enrollment::where('user_id', $user->id)
        ->where('course_id', $masterclass->id)
        ->first();

    expect($enrollment->subscription_granted)->toBeFalse();
    expect($user->fresh()->hasAccess($masterclass->id))->toBeTrue();
});
