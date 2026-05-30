<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Book;
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

        $query = Payment::query()->with(['user:id,name,email', 'course:id,title', 'book:id,title']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('book', fn ($b) => $b->where('title', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        if ($type = $request->input('type')) {
            if ($type === 'course') {
                $query->whereNotNull('course_id');
            } elseif ($type === 'membership') {
                $query->whereNull('course_id')->whereNull('book_id')->whereNotNull('subscription_type');
            } elseif ($type === 'books') {
                $query->whereNotNull('book_id');
            }
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Only load courses (small dataset) — users are fetched via AJAX search
        $courses = Course::select('id', 'title', 'price', 'sale_price')
            ->where('status', 'PUBLICADO')
            ->orderBy('title')
            ->get();

        $books = Book::select('id', 'title', 'price')
            ->where('is_available', true)
            ->where('price', '>', 0)
            ->orderBy('title')
            ->get();

        return Inertia::render('admin/Payments', [
            'payments' => $payments,
            'filters' => $request->only('status', 'search', 'date', 'per_page', 'type'),
            'stats' => [
                'total' => Payment::count(),
                'pendiente' => Payment::where('status', 'pendiente')->count(),
                'en_revision' => Payment::where('status', 'en_revision')->count(),
                'aprobado' => Payment::where('status', 'aprobado')->count(),
                'rechazado' => Payment::where('status', 'rechazado')->count(),
                'book_income' => (float) Payment::where('status', 'aprobado')->whereNotNull('book_id')->sum('amount'),
                'book_sales' => Payment::where('status', 'aprobado')->whereNotNull('book_id')->count(),
            ],
            'courses' => $courses,
            'books' => $books,
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
            'course_id' => $data['product_type'] === 'course' ? $data['course_id'] : null,
            'book_id' => $data['product_type'] === 'book' ? $data['book_id'] : null,
            'amount' => $data['amount'],
            'status' => $data['status'] === 'aprobado' ? 'pendiente' : $data['status'],
            'comprobante' => $comprobanteUrl,
        ]);

        if ($data['status'] === 'aprobado') {
            $this->service->approve($payment);
        }

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['user', 'course', 'book', 'bookOrder']);

        return Inertia::render('admin/PaymentShow', [
            'payment' => $payment,
            'shippingStatuses' => \App\Models\BookOrder::statusLabels(),
        ]);
    }

    public function approve(Payment $payment)
    {
        $this->authorize('approve', $payment);

        $this->service->approve($payment);

        return redirect()->back()->with('success', $payment->book_id
            ? 'Pago aprobado — pedido de libro registrado.'
            : 'Pago aprobado — curso desbloqueado.');
    }

    public function reject(Payment $payment)
    {
        $this->authorize('reject', $payment);

        $this->service->reject($payment);

        return redirect()->back()->with('success', 'Pago rechazado.');
    }

    public function revert(Payment $payment)
    {
        $this->authorize('revert', $payment);

        $this->service->revert($payment);

        return redirect()->back()->with('success', 'Aprobación revertida — el pago volvió a revisión.');
    }
}
