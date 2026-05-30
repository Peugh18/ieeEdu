<?php

namespace App\Http\Requests\Student;

use App\Models\Book;
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
            'course_id' => ['required_without:book_id', 'nullable', 'exists:courses,id'],
            'book_id' => ['required_without:course_id', 'nullable', 'exists:books,id'],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            if ($this->filled('course_id') && $this->filled('book_id')) {
                $validator->errors()->add('course_id', 'Indica solo un producto a la vez.');

                return;
            }

            $userId = Auth::id();

            if ($this->filled('book_id')) {
                $this->validateBookPayment($validator, $userId, (int) $this->input('book_id'));

                return;
            }

            $this->validateCoursePayment($validator, $userId, (int) $this->input('course_id'));
        });
    }

    private function validateCoursePayment($validator, int $userId, int $courseId): void
    {
        $course = Course::find($courseId);

        if (! $course) {
            return;
        }

        if (in_array($course->type, ['evento', 'masterclass'], true)) {
            $validator->errors()->add('course_id', 'Las masterclasses se gestionan por WhatsApp, no por pago en línea.');

            return;
        }

        if ($course->effectivePrice() <= 0) {
            $validator->errors()->add('course_id', 'Este curso es gratuito. Usa "Acceder gratis".');

            return;
        }

        if ($course->status !== 'PUBLICADO') {
            $validator->errors()->add('course_id', 'Este curso no está disponible para compra.');

            return;
        }

        if ($this->user()->hasPermanentCourseAccess($courseId)) {
            $validator->errors()->add('course_id', 'Ya tienes acceso permanente a este curso.');

            return;
        }

        $pendingOrReview = Payment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->whereIn('status', ['pendiente', 'en_revision'])
            ->exists();

        if ($pendingOrReview) {
            $validator->errors()->add('course_id', 'Ya tienes un pago pendiente para este curso. Revisa "Mis Pagos".');
        }
    }

    private function validateBookPayment($validator, int $userId, int $bookId): void
    {
        $book = Book::find($bookId);

        if (! $book) {
            return;
        }

        if (! $book->isPaid()) {
            $validator->errors()->add('book_id', 'Este libro es gratuito. Usa "Descargar".');

            return;
        }

        if (! $book->is_available) {
            $validator->errors()->add('book_id', 'Este libro no está disponible para compra.');

            return;
        }

        if ($this->user()->hasBookAccess($bookId)) {
            $validator->errors()->add('book_id', 'Ya tienes acceso a este libro.');

            return;
        }

        if (! $book->canAcceptNewPurchase()) {
            $validator->errors()->add('book_id', 'Este libro está agotado. No hay stock disponible.');

            return;
        }

        $pendingOrReview = Payment::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pendiente', 'en_revision'])
            ->exists();

        if ($pendingOrReview) {
            $validator->errors()->add('book_id', 'Ya tienes un pago pendiente para este libro. Revisa "Mis Pagos".');
        }
    }
}
