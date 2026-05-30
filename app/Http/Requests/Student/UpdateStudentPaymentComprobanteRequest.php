<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateStudentPaymentComprobanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $payment = $this->route('payment');

        return Auth::check()
            && $payment
            && (int) $payment->user_id === (int) Auth::id();
    }

    public function rules(): array
    {
        return [
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
