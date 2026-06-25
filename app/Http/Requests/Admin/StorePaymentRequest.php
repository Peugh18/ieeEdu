<?php

namespace App\Http\Requests\Admin;

use App\Models\Book;
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
            'product_type' => 'required|in:course,book',
            'course_id' => 'required_if:product_type,course|nullable|exists:courses,id',
            'book_id' => 'required_if:product_type,book|nullable|exists:books,id',
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
            $productType = $this->input('product_type');

            if ($productType === 'course') {
                $courseId = $this->input('course_id');
                $user = User::find($userId);

                if ($user && $user->hasAccess($courseId)) {
                    $validator->errors()->add('course_id', 'Este estudiante ya tiene acceso a este curso.');

                    return;
                }

                $pendingOrApproved = Payment::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->whereIn('status', ['aprobado', 'en_revision', 'pendiente'])
                    ->exists();

                if ($pendingOrApproved) {
                    $validator->errors()->add('course_id', 'Ya existe un pago registrado para este estudiante en este curso.');
                }

                return;
            }

            $bookId = $this->input('book_id');
            $book = Book::find($bookId);

            if ($book && ! $book->canAcceptNewPurchase()) {
                $user = User::find($userId);
                if (! $user || ! $user->hasBookAccess((int) $bookId)) {
                    $validator->errors()->add('book_id', 'Este libro no tiene stock disponible.');
                }
            }

            $pendingOrApproved = Payment::where('user_id', $userId)
                ->where('book_id', $bookId)
                ->whereIn('status', ['aprobado', 'en_revision', 'pendiente'])
                ->exists();

            if ($pendingOrApproved) {
                $validator->errors()->add('book_id', 'Ya existe un pago registrado para este estudiante y este libro.');
            }
        });
    }
}
