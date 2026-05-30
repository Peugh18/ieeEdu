<?php

namespace App\Support;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PlanPricing
{
    private const CACHE_KEY = 'subscription_plans.all';

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return Collection<int, SubscriptionPlan> */
    public static function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            if (! class_exists(SubscriptionPlan::class)) {
                return collect();
            }

            $plans = SubscriptionPlan::query()->ordered()->get();

            if ($plans->isEmpty()) {
                return collect(self::fallbackPlans());
            }

            return $plans;
        });
    }

    public static function find(string $slug): ?SubscriptionPlan
    {
        return self::all()->firstWhere('slug', $slug);
    }

    public static function price(string $slug): float
    {
        $plan = self::find($slug);

        if ($plan) {
            return (float) $plan->price;
        }

        $configured = config("iie.planes.{$slug}.price");

        if (is_numeric($configured) && (float) $configured > 0) {
            return (float) $configured;
        }

        return match ($slug) {
            'trimestral' => 350.0,
            'semestral' => 600.0,
            'anual' => 990.0,
            default => 0.0,
        };
    }

    public static function months(string $slug): int
    {
        $plan = self::find($slug);

        if ($plan) {
            return max(1, (int) $plan->months);
        }

        return match ($slug) {
            'trimestral' => 3,
            'semestral' => 6,
            'anual' => 12,
            default => 1,
        };
    }

    public static function name(string $slug): string
    {
        return self::find($slug)?->name ?? (config("iie.planes.{$slug}.name") ?? ucfirst($slug));
    }

    /** @return array<int, string> */
    public static function activeSlugs(): array
    {
        return self::all()
            ->where('is_active', true)
            ->pluck('slug')
            ->values()
            ->all();
    }

    /** @return array<int, string> */
    public static function allSlugs(): array
    {
        return self::all()->pluck('slug')->values()->all();
    }

    /** @return array<int, array<string, mixed>> */
    public static function activePlansForPublic(): array
    {
        $plans = self::all()->where('is_active', true);

        if ($plans->isEmpty()) {
            return array_values(array_map(
                fn (SubscriptionPlan $plan) => $plan->toPublicConfig(),
                self::fallbackPlans(),
            ));
        }

        return $plans->map(fn (SubscriptionPlan $plan) => $plan->toPublicConfig())->values()->all();
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminOptions(): array
    {
        return self::all()->map(fn (SubscriptionPlan $plan) => [
            'slug' => $plan->slug,
            'name' => $plan->name,
            'price' => (float) $plan->price,
            'months' => (int) $plan->months,
        ])->values()->all();
    }

    /** @return array<int, SubscriptionPlan> */
    private static function fallbackPlans(): array
    {
        $result = [];
        $order = 0;

        foreach (config('iie.planes', []) as $slug => $plan) {
            $months = match ($slug) {
                'trimestral' => 3,
                'semestral' => 6,
                'anual' => 12,
                default => 1,
            };

            $model = new SubscriptionPlan([
                'slug' => $slug,
                'name' => $plan['name'] ?? ucfirst($slug),
                'price' => $plan['price'] ?? self::price($slug),
                'months' => $months,
                'period_label' => $plan['period'] ?? "{$months} meses",
                'badge' => $plan['badge'] ?? null,
                'description' => $plan['description'] ?? null,
                'features' => SubscriptionPlan::defaultFeatures(),
                'sort_order' => $order++,
                'is_active' => true,
            ]);

            $result[] = $model;
        }

        return $result;
    }
}
