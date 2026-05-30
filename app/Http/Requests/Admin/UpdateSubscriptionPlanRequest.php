<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
            'months' => ['required', 'integer', 'min:1', 'max:60'],
            'period_label' => ['required', 'string', 'max:60'],
            'badge' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
            'features' => ['nullable', 'array', 'max:20'],
            'features.*' => ['string', 'max:300'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('features_text') && ! $this->has('features')) {
            $lines = preg_split('/\r\n|\r|\n/', (string) $this->input('features_text')) ?: [];
            $features = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));

            $this->merge(['features' => $features]);
        }
    }
}
