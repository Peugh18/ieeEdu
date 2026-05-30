<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Support\PlanPricing;
use Illuminate\Support\Facades\Cache;

class SiteSettingsService
{
    private const CACHE_KEY = 'site_settings.all';

    /** @var array<string, string|null> */
    private static array $defaults = [
        'whatsapp_sales' => '51959166911',
        'whatsapp_support' => '51934057867',
        'social_facebook' => '',
        'social_linkedin' => '',
        'contact_email' => 'info@iee.edu.pe',
        'contact_address' => 'Trujillo, La Libertad — Perú',
    ];

    /** @return array<string, string|null> */
    public static function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $stored = SiteSetting::query()->pluck('value', 'key')->all();

            return array_merge(self::$defaults, array_map(
                fn ($value) => $value === null ? null : (string) $value,
                $stored,
            ));
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = self::all();
        $fallback = $default ?? (self::$defaults[$key] ?? null);

        $value = $all[$key] ?? $fallback;

        return $value === null || $value === '' ? $fallback : $value;
    }

    public static function whatsappSales(): string
    {
        return preg_replace('/\D+/', '', (string) self::get('whatsapp_sales', config('iie.whatsapp_sales', '51959166911'))) ?: '51959166911';
    }

    /** @return array<string, string|null> */
    public static function sharedForInertia(): array
    {
        $settings = self::all();

        return [
            'whatsapp_sales' => self::whatsappSales(),
            'whatsapp_support' => preg_replace('/\D+/', '', (string) ($settings['whatsapp_support'] ?? '')) ?: null,
            'social_facebook' => $settings['social_facebook'] ?? '',
            'social_linkedin' => $settings['social_linkedin'] ?? '',
            'contact_email' => $settings['contact_email'] ?? self::$defaults['contact_email'],
            'contact_address' => $settings['contact_address'] ?? self::$defaults['contact_address'],
        ];
    }

    /** @param  array<string, string|null>  $settings */
    public static function update(array $settings): void
    {
        foreach ($settings as $key => $value) {
            if (! array_key_exists($key, self::$defaults)) {
                continue;
            }

            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        PlanPricing::clearCache();
    }
}
