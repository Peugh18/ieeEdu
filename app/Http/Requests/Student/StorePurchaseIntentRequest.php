<?php

namespace App\Http\Requests\Student;

use App\Models\Book;
use App\Models\Course;
use App\Models\Payment;
use App\Support\PlanPricing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePurchaseIntentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required_without_all:book_id,plan', 'nullable', 'exists:courses,id'],
            'book_id' => ['required_without_all:course_id,plan', 'nullable', 'exists:books,id'],
            'plan' => ['required_without_all:course_id,book_id', 'nullable', 'string', Rule::in(PlanPricing::activeSlugs())],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $fields = ['course_id', 'book_id', 'plan'];
            $filled = collect($fields)->filter(fn (string $field) => $this->filled($field))->count();

            if ($filled > 1) {
                $validator->errors()->add('course_id', 'Indica solo un producto a la vez.');

                return;
            }

            $userId = Auth::id();

            if ($this->filled('plan')) {
                $this->validateSubscription($validator);

                return;
            }

            if ($this->filled('book_id')) {
                $this->validateBook($validator, $userId, (int) $this->input('book_id'));

                return;
            }

            $this->validateCourse($validator, $userId, (int) $this->input('course_id'));
        });
    }

    private function validateSubscription($validator): void
    {
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
            $validator->errors()->add('plan', 'Ya tienes una solicitud de membresía activa. Revisa Mis Pagos.');
        }
    }

    private function validateCourse($validator, int $userId, int $courseId): void
    {
        $course = Course::find($courseId);

        if (! $course) {
            return;
        }

        if (in_array($course->type, ['evento', 'masterclass'], true)) {
            $validator->errors()->add('course_id', 'Las masterclasses se gestionan por WhatsApp directamente.');

            return;
        }

        if ($course->effectivePrice() <= 0) {
            $validator->errors()->add('course_id', 'Este curso es gratuito.');

            return;
        }

        if ($course->status !== 'PUBLICADO') {
            $validator->errors()->add('course_id', 'Este curso no está disponible.');

            return;
        }

        if ($this->user()->hasPermanentCourseAccess($courseId)) {
            $validator->errors()->add('course_id', 'Ya tienes acceso a este curso.');

            return;
        }

        if ($this->hasActivePayment($userId, 'course_id', $courseId)) {
            $validator->errors()->add('course_id', 'Ya tienes una solicitud activa. Revisa Mis Pagos.');
        }
    }

    private function validateBook($validator, int $userId, int $bookId): void
    {
        $book = Book::find($bookId);

        if (! $book) {
            return;
        }

        if (! $book->isPaid()) {
            $validator->errors()->add('book_id', 'Este libro es gratuito.');

            return;
        }

        if (! $book->is_available) {
            $validator->errors()->add('book_id', 'Este libro no está disponible.');

            return;
        }

        if ($this->user()->hasApprovedBookPayment($bookId)) {
            $validator->errors()->add('book_id', 'Ya tienes un pedido activo para este libro.');

            return;
        }

        if (! $book->canAcceptNewPurchase()) {
            $validator->errors()->add('book_id', 'Este libro está agotado.');

            return;
        }

        if ($this->hasActivePayment($userId, 'book_id', $bookId)) {
            $validator->errors()->add('book_id', 'Ya tienes una solicitud activa. Revisa Mis Pagos.');
        }
    }

    private function hasActivePayment(int $userId, string $column, int $id): bool
    {
        return Payment::where('user_id', $userId)
            ->where($column, $id)
            ->whereIn('status', ['pendiente', 'en_revision', 'aprobado'])
            ->exists();
    }
}
