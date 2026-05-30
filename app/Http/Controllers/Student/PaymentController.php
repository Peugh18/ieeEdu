<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentPaymentRequest;
use App\Http\Requests\Student\UpdateStudentPaymentComprobanteRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $payments = Payment::where('user_id', $user->id)
            ->with('course:id,title,slug,image')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('student/Payments', [
            'payments' => $payments,
        ]);
    }

    public function store(StoreStudentPaymentRequest $request)
    {
        $this->authorize('create', Payment::class);

        $user = Auth::user();
        $data = $request->validated();

        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $this->storeComprobanteFile($request->file('comprobante'));
        }

        Payment::create([
            'user_id' => $user->id,
            'course_id' => $data['course_id'],
            'amount' => $data['amount'],
            'status' => 'pendiente',
            'comprobante' => $comprobanteUrl,
        ]);

        return redirect()->route('student.payments.index')
            ->with('success', 'Pago registrado. Te notificaremos cuando sea validado.');
    }

    public function updateComprobante(UpdateStudentPaymentComprobanteRequest $request, Payment $payment)
    {
        $this->authorize('uploadComprobante', $payment);

        $comprobanteUrl = $this->storeComprobanteFile($request->file('comprobante'));

        $payment->update([
            'comprobante' => $comprobanteUrl,
            'status' => 'en_revision',
        ]);

        return redirect()->route('student.payments.index')
            ->with('success', 'Comprobante actualizado. Estamos revisando tu pago.');
    }

    private function storeComprobanteFile($file): string
    {
        $ext = $file->getClientOriginalExtension();
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'_'.time();
        $filename = $safeName.'.'.$ext;
        $path = $file->storeAs('comprobantes', $filename, 'public');

        return Storage::url($path);
    }
}
