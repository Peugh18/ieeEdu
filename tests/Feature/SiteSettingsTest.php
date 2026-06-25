<?php

use App\Models\SubscriptionPlan;
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
        'social_instagram' => 'https://instagram.com/iee.edu.pe',
        'social_youtube' => 'https://youtube.com/c/iee',
        'social_tiktok' => 'https://tiktok.com/@iee.edu.pe',
        'social_twitter' => 'https://x.com/iee',
        'contact_email' => 'ventas@iee.edu.pe',
        'contact_address' => 'Trujillo, Perú',
    ]);

    $response->assertRedirect();
    SiteSettingsService::clearCache();

    expect(SiteSettingsService::whatsappSales())->toBe('51999888777');
    expect(SiteSettingsService::get('social_facebook'))->toBe('https://facebook.com/iee.edu.pe');
    expect(SiteSettingsService::get('social_instagram'))->toBe('https://instagram.com/iee.edu.pe');
    expect(SiteSettingsService::get('social_youtube'))->toBe('https://youtube.com/c/iee');
    expect(SiteSettingsService::get('social_tiktok'))->toBe('https://tiktok.com/@iee.edu.pe');
    expect(SiteSettingsService::get('social_twitter'))->toBe('https://x.com/iee');
});

test('admin can update subscription plan price and months', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $plan = SubscriptionPlan::where('slug', 'trimestral')->first();
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
    $plan = SubscriptionPlan::where('slug', 'anual')->first();
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
