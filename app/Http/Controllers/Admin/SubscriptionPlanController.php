<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use App\Support\PlanPricing;
use Inertia\Inertia;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/SubscriptionPlans', [
            'plans' => PlanPricing::all()->map(fn (SubscriptionPlan $plan) => [
                'id' => $plan->id,
                'slug' => $plan->slug,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'months' => (int) $plan->months,
                'period_label' => $plan->period_label,
                'badge' => $plan->badge,
                'description' => $plan->description,
                'features' => $plan->featureList(),
                'sort_order' => (int) $plan->sort_order,
                'is_active' => (bool) $plan->is_active,
            ])->values(),
        ]);
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update($request->validated());
        PlanPricing::clearCache();

        return back()->with('success', "Plan {$subscriptionPlan->name} actualizado.");
    }
}
