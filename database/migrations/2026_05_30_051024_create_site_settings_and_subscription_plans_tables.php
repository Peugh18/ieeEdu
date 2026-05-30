<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('months');
            $table->string('period_label');
            $table->string('badge')->nullable();
            $table->string('badge_icon')->nullable();
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        foreach ([
            ['key' => 'whatsapp_sales', 'value' => config('iie.whatsapp_sales', '51959166911')],
            ['key' => 'whatsapp_support', 'value' => config('iie.whatsapp_support', '51934057867')],
            ['key' => 'social_facebook', 'value' => ''],
            ['key' => 'social_linkedin', 'value' => ''],
            ['key' => 'contact_email', 'value' => 'info@iee.edu.pe'],
            ['key' => 'contact_address', 'value' => 'Trujillo, La Libertad — Perú'],
        ] as $setting) {
            DB::table('site_settings')->insert([
                ...$setting,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $plans = config('iie.planes', []);
        $order = 0;
        foreach (['trimestral', 'semestral', 'anual'] as $slug) {
            $plan = $plans[$slug] ?? [];
            $months = match ($slug) {
                'trimestral' => 3,
                'semestral' => 6,
                'anual' => 12,
                default => 1,
            };

            DB::table('subscription_plans')->insert([
                'slug' => $slug,
                'name' => $plan['name'] ?? ucfirst($slug),
                'price' => $plan['price'] ?? 0,
                'months' => $months,
                'period_label' => $plan['period'] ?? "{$months} meses",
                'badge' => $plan['badge'] ?? null,
                'badge_icon' => $plan['badgeIcon'] ?? null,
                'description' => $plan['description'] ?? null,
                'color' => $plan['color'] ?? null,
                'sort_order' => $order++,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('site_settings');
    }
};
