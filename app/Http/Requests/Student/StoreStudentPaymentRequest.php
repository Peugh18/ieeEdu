<?php

namespace App\Http\Requests\Student;

use App\Models\Course;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStudentPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $userId = Auth::id();
            $courseId = $this->input('course_id');
            $course = Course::find($courseId);

            if (! $course) {
                return;
            }

            // Validar que el monto sea coherente con el precio del curso
            $expectedPrice = $course->sale_price > 0 ? $course->sale_price : $course->price;
            $amount = (float) $this->input('amount');

            if ($amount < $expectedPrice * 0.5 || $amount > $expectedPrice * 2) {
                $validator->errors()->add('amount', 'El monto no parece coincidir con el precio del curso.');

                return;
            }

            // Guard: ¿Ya tiene acceso a este curso?
            if ($this->user()->hasAccess($courseId)) {
                $validator->errors()->add('course_id', 'Ya tienes acceso a este curso.');

                return;
            }

            // Guard: ¿Ya hay un pago pendiente o en revisión?
            $pendingOrReview = Payment::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->whereIn('status', ['pendiente', 'en_revision'])
                ->exists();

            if ($pendingOrReview) {
                $validator->errors()->add('course_id', 'Ya tienes un pago pendiente para este curso. Revisa "Mis Pagos".');
            }
        });
    }
}
