<?php

namespace App\Http\Requests\Admin;

use App\Models\BookOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateBookOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'shipping_status' => ['required', Rule::in([
                BookOrder::STATUS_AWAITING_ADDRESS,
                BookOrder::STATUS_PREPARING,
                BookOrder::STATUS_SHIPPED,
                BookOrder::STATUS_DELIVERED,
                BookOrder::STATUS_CANCELLED,
            ])],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'shipping_phone' => ['nullable', 'string', 'max:20'],
            'delivery_mode' => ['nullable', Rule::in(['home', 'pickup'])],
            'pickup_location' => ['nullable', 'string', 'max:500'],
            'carrier' => ['nullable', 'string', 'max:120'],
            'tracking_url' => ['nullable', 'url', 'max:500'],
            'tracking_code' => ['nullable', 'string', 'max:120'],
            'student_note' => ['nullable', 'string', 'max:1000'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
