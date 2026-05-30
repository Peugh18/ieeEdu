<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreSubscriptionPaymentRequest;
use App\Models\Payment;
use App\Support\PlanPricing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SubscriptionPaymentController extends Controller
{
    public function store(StoreSubscriptionPaymentRequest $request)
    {
        $user = Auth::user();
        $plan = $request->input('plan');
        $price = PlanPricing::price($plan);

        $path = $request->file('comprobante')->store('comprobantes', 'public');

        Payment::create([
            'user_id' => $user->id,
            'course_id' => null,
            'subscription_type' => $plan,
            'amount' => $price,
            'status' => 'en_revision',
            'comprobante' => Storage::url($path),
        ]);

        return redirect()->route('student.subscriptions.payment.status')
            ->with('success', 'El comprobante de su membresía ha sido enviado para su revisión.');
    }

    public function status()
    {
        return Inertia::render('student/SubscriptionPaymentStatus');
    }
}
