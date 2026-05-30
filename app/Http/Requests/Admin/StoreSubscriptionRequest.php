<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'type' => ['required', Rule::in(['trimestral', 'semestral', 'anual'])],
            'months' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'comprobante' => 'nullable|file|image|max:5120',
        ];
    }
}
