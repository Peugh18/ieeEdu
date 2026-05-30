<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubscriptionRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\SubscriptionAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionAccessService $accessService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Subscription::class);

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

        // Optimización N+1: Vincular pagos adjuntando monto y captura si existe en una consulta única
        $userIds = $subscriptions->pluck('user_id')->unique()->toArray();
        $payments = Payment::whereIn('user_id', $userIds)
            ->where('status', 'aprobado')
            ->whereNull('course_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        $subscriptions->getCollection()->transform(function ($sub) use ($payments) {
            $userPayments = $payments->get($sub->user_id);
            $payment = $userPayments ? $userPayments->first() : null;

            $sub->payment_amount = $payment ? $payment->amount : null;
            $sub->payment_capture = $payment ? $payment->comprobante : null;

            return $sub;
        });

        return Inertia::render('admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only('search', 'per_page'),
        ]);
    }

    public function store(StoreSubscriptionRequest $request)
    {
        $this->authorize('create', Subscription::class);

        $data = $request->validated();

        try {
            // Desactivar suscripciones activas previas (sin Observer: usamos update masivo)
            Subscription::where('user_id', $data['user_id'])
                ->where('status', Subscription::STATUS_ACTIVE)
                ->update(['status' => Subscription::STATUS_EXPIRED]);

            // Crear nueva suscripción (el Observer la ignorará porque no fue updated)
            $sub = Subscription::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'start_date' => now(),
                'end_date' => now()->addMonths((int) $data['months']),
                'status' => Subscription::STATUS_ACTIVE,
            ]);

            // Gestión de comprobante (H4: sanitizar nombre de archivo)
            $comprobantePath = null;
            if ($request->hasFile('comprobante')) {
                $file = $request->file('comprobante');
                $ext = $file->getClientOriginalExtension();
                $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'_'.time();
                $filename = $safeName.'.'.$ext;
                $path = $file->storeAs('comprobantes', $filename, 'public');
                $comprobantePath = '/storage/'.$path;
            }

            // Registrar pago
            Payment::create([
                'user_id' => $data['user_id'],
                'amount' => $data['amount'],
                'status' => 'aprobado',
                'comprobante' => $comprobantePath ?: ('Membresía '.$data['type'].' - Activación Admin'),
                'course_id' => null,
            ]);

            // Otorgar acceso mediante el servicio (con flags de suscripción)
            $this->accessService->grantAccess($data['user_id']);

            return back()->with('success', 'Membresía '.$data['type'].' activada. Acceso total a cursos habilitado.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar la suscripción: '.$e->getMessage());
        }
    }

    /**
     * Toggle activa ↔ cancelada.
     * El Observer detecta el cambio de status y sincroniza el acceso automáticamente.
     */
    public function toggleStatus(Subscription $subscription)
    {
        $this->authorize('toggle', $subscription);

        $subscription->status = $subscription->status === Subscription::STATUS_ACTIVE
            ? Subscription::STATUS_CANCELLED
            : Subscription::STATUS_ACTIVE;
        $subscription->save(); // <-- Observer disparado aquí

        return back()->with('success', 'Estado de suscripción actualizado a '.$subscription->status.'.');
    }

    /**
     * Eliminar.
     * El Observer detecta el deleted event y revoca el acceso automáticamente.
     */
    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        $subscription->delete(); // <-- Observer disparado aquí

        return back()->with('success', 'Suscripción eliminada del historial.');
    }
}
