<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service)
    {
    }

    public function index(Request $request)
    {
        $payments = $this->service->list(15, $request->only('status', 'search'));

        return Inertia::render('admin/Payments', [
            'payments' => $payments,
            'filters' => $request->only('status', 'search'),
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'course']);

        return Inertia::render('admin/PaymentShow', [
            'payment' => $payment,
        ]);
    }

    public function approve(Payment $payment)
    {
        $this->service->approve($payment);

        return redirect()->back()->with('success', 'Pago aprobado y curso desbloqueado.');
    }

    public function reject(Payment $payment)
    {
        $this->service->reject($payment);

        return redirect()->back()->with('success', 'Pago rechazado.');
    }
}
