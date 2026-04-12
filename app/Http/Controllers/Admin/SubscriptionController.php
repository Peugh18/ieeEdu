<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\SubscriptionAccessService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionAccessService $accessService
    ) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $query = Subscription::query()->with('user:id,name,email');

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Vincular pagos: adjuntar monto y captura si existe
        $subscriptions->getCollection()->transform(function ($sub) {
            $payment = Payment::where('user_id', $sub->user_id)
                ->where('status', 'aprobado')
                ->whereNull('course_id')
                ->latest()
                ->first();
            
            $sub->payment_amount = $payment ? $payment->amount : null;
            $sub->payment_capture = $payment ? $payment->comprobante : null;
            return $sub;
        });

        return Inertia::render('admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'filters'       => $request->only('search', 'per_page'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'type'        => 'required|string',
            'months'      => 'required|integer|min:1',
            'amount'      => 'required|numeric|min:0',
            'comprobante' => 'nullable|file|image|max:5120',
        ]);

        try {
            // Desactivar suscripciones activas previas (sin Observer: usamos update masivo)
            Subscription::where('user_id', $data['user_id'])
                ->where('status', 'activa')
                ->update(['status' => 'expirada']);

            // Crear nueva suscripción (el Observer la ignorará porque no fue updated)
            $sub = Subscription::create([
                'user_id'    => $data['user_id'],
                'type'       => $data['type'],
                'start_date' => now(),
                'end_date'   => now()->addMonths((int) $data['months']),
                'status'     => 'activa',
            ]);

            // Gestión de comprobante
            $comprobantePath = null;
            if ($request->hasFile('comprobante')) {
                $file     = $request->file('comprobante');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path     = $file->storeAs('comprobantes', $filename, 'public');
                $comprobantePath = '/storage/' . $path;
            }

            // Registrar pago
            Payment::create([
                'user_id'     => $data['user_id'],
                'amount'      => $data['amount'],
                'status'      => 'aprobado',
                'comprobante' => $comprobantePath ?: ('Membresía ' . $data['type'] . ' - Activación Admin'),
                'course_id'   => null,
            ]);

            // Otorgar acceso mediante el servicio (con flags de suscripción)
            $this->accessService->grantAccess($data['user_id']);

            return back()->with('success', 'Membresía ' . $data['type'] . ' activada. Acceso total a cursos habilitado.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar la suscripción: ' . $e->getMessage());
        }
    }

    /**
     * Toggle activa ↔ cancelada.
     * El Observer detecta el cambio de status y sincroniza el acceso automáticamente.
     */
    public function toggleStatus(Subscription $subscription)
    {
        $subscription->status = $subscription->status === 'activa' ? 'cancelada' : 'activa';
        $subscription->save(); // <-- Observer disparado aquí

        return back()->with('success', 'Estado de suscripción actualizado a ' . $subscription->status . '.');
    }

    /**
     * Eliminar.
     * El Observer detecta el deleted event y revoca el acceso automáticamente.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete(); // <-- Observer disparado aquí

        return back()->with('success', 'Suscripción eliminada del historial.');
    }
}
