<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookOrderRequest;
use App\Models\BookOrder;
use App\Services\BookOrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookOrderController extends Controller
{
    public function __construct(protected BookOrderService $orders) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50]) ? $perPage : 20;

        $query = BookOrder::query()
            ->with([
                'book:id,title,cover_image,price',
                'user:id,name,email,telefono',
                'payment:id,status,amount,comprobante,created_at',
            ])
            ->latest();

        if ($status = $request->input('status')) {
            $query->where('shipping_status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('book', fn ($b) => $b->where('title', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate($perPage)->withQueryString();

        return Inertia::render('admin/BookOrders', [
            'orders' => $orders,
            'filters' => $request->only('status', 'search', 'per_page'),
            'stats' => [
                'awaiting_address' => BookOrder::where('shipping_status', BookOrder::STATUS_AWAITING_ADDRESS)->count(),
                'preparing' => BookOrder::where('shipping_status', BookOrder::STATUS_PREPARING)->count(),
                'shipped' => BookOrder::where('shipping_status', BookOrder::STATUS_SHIPPED)->count(),
                'delivered' => BookOrder::where('shipping_status', BookOrder::STATUS_DELIVERED)->count(),
            ],
            'statusLabels' => BookOrder::statusLabels(),
        ]);
    }

    public function update(UpdateBookOrderRequest $request, BookOrder $bookOrder)
    {
        $this->orders->updateShipping($bookOrder, $request->validated());

        return redirect()->back()->with('success', 'Envío actualizado correctamente.');
    }
}
