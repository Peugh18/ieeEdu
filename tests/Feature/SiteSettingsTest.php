<?php

use App\Models\User;
use App\Services\SiteSettingsService;
use App\Support\PlanPricing;

test('admin can update company whatsapp and social settings', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->patch(route('admin.settings.company.update'), [
        'whatsapp_sales' => '51999888777',
        'whatsapp_support' => '51911223344',
        'social_facebook' => 'https://facebook.com/iee.edu.pe',
        'social_linkedin' => 'https://linkedin.com/company/iee',
        'contact_email' => 'ventas@iee.edu.pe',
        'contact_address' => 'Trujillo, Perú',
    ]);

    $response->assertRedirect();
    SiteSettingsService::clearCache();

    expect(SiteSettingsService::whatsappSales())->toBe('51999888777');
    expect(SiteSettingsService::get('social_facebook'))->toBe('https://facebook.com/iee.edu.pe');
});

test('admin can update subscription plan price and months', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $plan = \App\Models\SubscriptionPlan::where('slug', 'trimestral')->first();
    expect($plan)->not->toBeNull();

    $this->actingAs($admin)->patch(route('admin.settings.plans.update', $plan), [
        'name' => 'Plan 3 Meses',
        'price' => 399,
        'months' => 3,
        'period_label' => '3 meses',
        'badge' => 'Nuevo precio',
        'description' => 'Acceso premium trimestral',
        'features' => ['Beneficio uno', 'Beneficio dos'],
        'sort_order' => 0,
        'is_active' => true,
    ])->assertRedirect();

    PlanPricing::clearCache();

    expect(PlanPricing::price('trimestral'))->toBe(399.0);
    expect(PlanPricing::months('trimestral'))->toBe(3);
    expect(PlanPricing::name('trimestral'))->toBe('Plan 3 Meses');
    expect(PlanPricing::find('trimestral')?->featureList())->toBe(['Beneficio uno', 'Beneficio dos']);
});

test('planes page uses active plans from database', function () {
    $plan = \App\Models\SubscriptionPlan::where('slug', 'anual')->first();
    $plan->update(['price' => 1200, 'period_label' => '12 meses']);
    PlanPricing::clearCache();

    $response = $this->get(route('planes'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Planes')
        ->has('planesConfig', 3)
        ->where('planesConfig.2.price', 1200)
    );
});
