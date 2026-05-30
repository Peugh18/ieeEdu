<?php

namespace App\Http\Requests\Admin;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pendiente,en_revision,aprobado,rechazado',
            'comprobante' => 'nullable|image|max:5120',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $userId = $this->input('user_id');
            $courseId = $this->input('course_id');

            // ── Guard: ¿Ya tiene acceso a este curso? ─────────────────
            $user = User::find($userId);
            if ($user && $user->hasAccess($courseId)) {
                $validator->errors()->add('course_id', 'Este estudiante ya tiene acceso a este curso.');

                return;
            }

            // ── Guard: ¿Ya hay un pago aprobado o en revisión? ────────
            $pendingOrApproved = Payment::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->whereIn('status', ['aprobado', 'en_revision', 'pendiente'])
                ->exists();

            if ($pendingOrApproved) {
                $validator->errors()->add('course_id', 'Ya existe un pago registrado para este estudiante en este curso.');
            }
        });
    }
}
