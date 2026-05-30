<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreSubscriptionPaymentRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SubscriptionPaymentController extends Controller
{
    public function store(StoreSubscriptionPaymentRequest $request)
    {
        $user = Auth::user();
        $plan = $request->input('plan');

        $price = config("iie.planes.{$plan}.price", 0);

        $path = $request->file('comprobante')->store('comprobantes', 'public');

        Payment::create([
            'user_id' => $user->id,
            'course_id' => null,
            'subscription_type' => $plan,
            'amount' => $price,
            'status' => 'en_revision',
            'comprobante' => $path,
        ]);

        return redirect()->route('student.subscriptions.payment.status')
            ->with('success', 'El comprobante de su membresía ha sido enviado para su revisión.');
    }

    public function status()
    {
        return Inertia::render('student/SubscriptionPaymentStatus');
    }
}
