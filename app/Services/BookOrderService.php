<?php

namespace App\Services;

use App\Models\BookOrder;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BookOrderService
{
    public function createForPayment(Payment $payment): BookOrder
    {
        return BookOrder::firstOrCreate(
            ['payment_id' => $payment->id],
            [
                'book_id' => $payment->book_id,
                'user_id' => $payment->user_id,
                'shipping_status' => BookOrder::STATUS_AWAITING_ADDRESS,
            ]
        );
    }

    public function updateShipping(BookOrder $order, array $data): BookOrder
    {
        $status = $data['shipping_status'] ?? $order->shipping_status;

        if ($status === BookOrder::STATUS_SHIPPED && empty($data['shipped_at']) && ! $order->shipped_at) {
            $data['shipped_at'] = now();
        }

        if ($status === BookOrder::STATUS_DELIVERED && empty($data['delivered_at']) && ! $order->delivered_at) {
            $data['delivered_at'] = now();
        }

        $data['updated_by'] = Auth::id();

        $order->update($data);

        Cache::forget('admin_dashboard_stats');

        return $order->fresh();
    }

    public function cancelForPayment(Payment $payment): void
    {
        $order = $payment->bookOrder;

        if ($order) {
            $order->update([
                'shipping_status' => BookOrder::STATUS_CANCELLED,
                'updated_by' => Auth::id(),
            ]);
        }
    }
}
