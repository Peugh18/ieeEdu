<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('dashboard expone email_verified_at para usuario no verificado', function () {
    $student = User::factory()->unverified()->create(['role' => 'usuario']);

    $response = $this->actingAs($student)->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('auth.user.email_verified_at', null)
    );
});

test('dashboard expone email_verified_at para usuario verificado', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $response = $this->actingAs($student)->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('auth.user.email_verified_at')
    );
});
