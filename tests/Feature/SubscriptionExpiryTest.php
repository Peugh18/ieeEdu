<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

it('revokes course access when subscription expires', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['price' => 100]);

    // Crear suscripción expirada pero aún con status activo
    $subscription = Subscription::create([
        'user_id' => $user->id,
        'type' => 'trimestral',
        'status' => Subscription::STATUS_ACTIVE,
        'start_date' => now()->subMonths(1),
        'end_date' => now()->subDay(),
    ]);

    // Crear enrollment por suscripción
    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'subscription_granted' => true,
        'subscription_active' => true,
    ]);

    // En este punto, como la end_date ya pasó, hasSubscriptionActive() debe ser false.
    // Y por lo tanto hasAccess() también.
    expect($user->hasSubscriptionActive())->toBeFalse();
    expect($user->hasAccess($course->id))->toBeFalse();

    // Ejecutar el comando para sincronizar
    Artisan::call('subscriptions:sync-expired');

    $subscription->refresh();
    expect($subscription->status)->toBe(Subscription::STATUS_EXPIRED);

    $enrollment = Enrollment::where('user_id', $user->id)->first();
    expect($enrollment->subscription_active)->toBeFalse();
});
