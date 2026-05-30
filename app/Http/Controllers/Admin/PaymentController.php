<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50]) ? $perPage : 20;

        $query = Payment::query()->with(['user:id,name,email', 'course:id,title']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Only load courses (small dataset) — users are fetched via AJAX search
        $courses = Course::select('id', 'title', 'price', 'sale_price')
            ->where('status', 'PUBLICADO')
            ->orderBy('title')
            ->get();

        return Inertia::render('admin/Payments', [
            'payments' => $payments,
            'filters' => $request->only('status', 'search', 'date', 'per_page'),
            'stats' => [
                'total' => Payment::count(),
                'pendiente' => Payment::where('status', 'pendiente')->count(),
                'en_revision' => Payment::where('status', 'en_revision')->count(),
                'aprobado' => Payment::where('status', 'aprobado')->count(),
                'rechazado' => Payment::where('status', 'rechazado')->count(),
            ],
            'courses' => $courses,
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $this->authorize('create', Payment::class);

        $data = $request->validated();

        // ── Upload comprobante ────────────────────────────────────
        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $comprobanteUrl = Storage::url($path);
        }

        $payment = Payment::create([
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id'],
            'amount' => $data['amount'],
            'status' => $data['status'],
            'comprobante' => $comprobanteUrl,
        ]);

        // Si se registra directamente como aprobado → crear enrollment
        if ($payment->status === 'aprobado') {
            $this->service->approve($payment);
        }

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['user', 'course']);

        return Inertia::render('admin/PaymentShow', [
            'payment' => $payment,
        ]);
    }

    public function approve(Payment $payment)
    {
        $this->authorize('approve', $payment);

        $this->service->approve($payment);

        return redirect()->back()->with('success', 'Pago aprobado — curso desbloqueado.');
    }

    public function reject(Payment $payment)
    {
        $this->authorize('reject', $payment);

        $this->service->reject($payment);

        return redirect()->back()->with('success', 'Pago rechazado.');
    }
}
