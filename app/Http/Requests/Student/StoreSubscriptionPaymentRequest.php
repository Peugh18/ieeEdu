<?php

namespace App\Http\Requests\Student;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan' => ['required', 'string', 'in:trimestral,semestral,anual'],
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
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
