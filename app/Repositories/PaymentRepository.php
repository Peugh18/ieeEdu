<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function paginate($perPage = 15, $filters = [])
    {
        $query = Payment::query()->with(['user', 'course']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$filters['search']}%"));
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find($id)
    {
        return Payment::with(['user', 'course'])->findOrFail($id);
    }

    public function update(Payment $payment, array $data)
    {
        $payment->update($data);

        return $payment;
    }

    public function create(array $data)
    {
        return Payment::create($data);
    }

    public function delete(Payment $payment)
    {
        return $payment->delete();
    }
}
