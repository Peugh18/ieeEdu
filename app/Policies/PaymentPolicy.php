<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->role === 'admin' || $user->id === $payment->user_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'usuario';
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }

    public function approve(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }

    public function reject(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }

    public function uploadComprobante(User $user, Payment $payment): bool
    {
        return $user->id === $payment->user_id
            && in_array($payment->status, ['pendiente', 'en_revision'], true);
    }
}
