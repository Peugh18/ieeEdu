<?php

namespace App\Observers;

use App\Models\Subscription;
use App\Services\SubscriptionAccessService;

/**
 * SubscriptionObserver
 *
 * Escucha eventos del modelo Subscription y sincroniza el acceso automáticamente.
 * Esto desacopla la lógica de acceso del controlador.
 */
class SubscriptionObserver
{
    public function __construct(
        protected SubscriptionAccessService $accessService
    ) {}

    /**
     * Cuando una suscripción cambia de estado (activa ↔ cancelada/expirada).
     */
    public function updated(Subscription $subscription): void
    {
        // Solo actúa si el campo 'status' fue el que cambió
        if ($subscription->wasChanged('status')) {
            $this->accessService->sync($subscription->user_id);
        }
    }

    /**
     * Cuando una suscripción es eliminada del historial.
     */
    public function deleted(Subscription $subscription): void
    {
        $this->accessService->sync($subscription->user_id);
    }
}
