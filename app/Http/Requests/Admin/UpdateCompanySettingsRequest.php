<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCompanySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'whatsapp_sales' => ['required', 'string', 'regex:/^[0-9]{9,15}$/'],
            'whatsapp_support' => ['nullable', 'string', 'regex:/^[0-9]{9,15}$/'],
            'social_facebook' => ['nullable', 'url', 'max:500'],
            'social_linkedin' => ['nullable', 'url', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp_sales.required' => 'El número de WhatsApp de ventas es obligatorio.',
            'whatsapp_sales.regex' => 'Ingresa el número con código de país, solo dígitos (ej. 51959166911).',
            'whatsapp_support.regex' => 'Ingresa el número con código de país, solo dígitos.',
            'social_facebook.url' => 'Ingresa una URL válida para Facebook.',
            'social_linkedin.url' => 'Ingresa una URL válida para LinkedIn.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'whatsapp_sales' => preg_replace('/\D+/', '', (string) $this->input('whatsapp_sales')),
            'whatsapp_support' => $this->filled('whatsapp_support')
                ? preg_replace('/\D+/', '', (string) $this->input('whatsapp_support'))
                : null,
        ]);
    }
}
