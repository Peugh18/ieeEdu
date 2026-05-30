<?php

namespace App\Http\Requests\Student;

use App\Models\Payment;
use App\Support\PlanPricing;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSubscriptionPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan' => ['required', 'string', Rule::in(PlanPricing::activeSlugs())],
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $user = $this->user();

            if ($user->hasSubscriptionActive()) {
                $validator->errors()->add('plan', 'Ya tienes una membresía Premium activa.');

                return;
            }

            $pending = Payment::where('user_id', $user->id)
                ->whereNull('course_id')
                ->whereNotNull('subscription_type')
                ->whereIn('status', ['pendiente', 'en_revision'])
                ->exists();

            if ($pending) {
                $validator->errors()->add('plan', 'Ya tienes un pago de membresía en revisión.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'plan.required' => 'Debe seleccionar un plan válido.',
            'plan.in' => 'El plan seleccionado no es válido.',
            'comprobante.required' => 'Debe subir un comprobante de pago.',
            'comprobante.file' => 'El comprobante debe ser un archivo.',
            'comprobante.mimes' => 'El comprobante debe estar en formato: jpg, jpeg, png o pdf.',
            'comprobante.max' => 'El tamaño del comprobante no puede superar los 2MB.',
        ];
    }
}
