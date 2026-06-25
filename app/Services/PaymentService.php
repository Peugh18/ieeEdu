<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\PaymentRejectedNotification;
use App\Repositories\EnrollmentRepository;
use App\Repositories\PaymentRepository;
use App\Support\PlanPricing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        protected PaymentRepository $repo,
        protected EnrollmentRepository $enrollmentRepo,
        protected EnrollmentService $enrollmentService
    ) {}

    public function list($perPage = 15, $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function approve(Payment $payment)
    {
        if (! in_array($payment->status, ['pendiente', 'en_revision'], true)) {
            throw ValidationException::withMessages([
                'payment' => 'Solo se pueden aprobar pagos pendientes o en revisión.',
            ]);
        }

        $payment->status = 'aprobado';
        $payment->save();

        if ($payment->course_id) {
            $this->enrollmentService->enroll($payment->user, $payment->course, $payment->id);
        } elseif ($payment->book_id) {
            DB::transaction(function () use ($payment) {
                $book = Book::query()->lockForUpdate()->find($payment->book_id);

                if (! $book) {
                    throw ValidationException::withMessages([
                        'payment' => 'El libro asociado a este pago ya no existe.',
                    ]);
                }

                if ($book->isPaid() && ! $book->hasUnlimitedStock()) {
                    if ($book->stock < 1) {
                        throw ValidationException::withMessages([
                            'payment' => 'Sin stock disponible para completar esta venta.',
                        ]);
                    }

                    $book->decrement('stock');
                }

                app(BookOrderService::class)->createForPayment($payment);
            });
        } elseif ($payment->subscription_type) {
            if ((float) $payment->amount <= 0) {
                $payment->amount = PlanPricing::price($payment->subscription_type);
                $payment->save();
            }

            $months = PlanPricing::months($payment->subscription_type);

            Subscription::updateOrCreate(
                ['user_id' => $payment->user_id],
                [
                    'type' => $payment->subscription_type,
                    'status' => Subscription::STATUS_ACTIVE,
                    'start_date' => now(),
                    'end_date' => now()->addMonths($months),
                ]
            );

            app(SubscriptionAccessService::class)->sync($payment->user_id);
        }

        $payment->user->notify(new PaymentApprovedNotification($payment));

        Cache::forget('admin_dashboard_stats');

        return $payment;
    }

    public function reject(Payment $payment)
    {
        $payment->status = 'rechazado';
        $payment->save();

        $payment->user->notify(new PaymentRejectedNotification($payment));

        return $payment;
    }

    /**
     * Revierte una aprobación errónea: devuelve el pago a revisión y revoca el acceso otorgado.
     */
    public function revert(Payment $payment)
    {
        if ($payment->status !== 'aprobado') {
            throw ValidationException::withMessages([
                'payment' => 'Solo se puede revertir un pago aprobado.',
            ]);
        }

        $payment->status = 'en_revision';
        $payment->save();

        if ($payment->course_id) {
            Enrollment::where('user_id', $payment->user_id)
                ->where('course_id', $payment->course_id)
                ->where('payment_id', $payment->id)
                ->where('subscription_granted', false)
                ->delete();
        } elseif ($payment->subscription_type) {
            $hasOtherApproved = Payment::where('user_id', $payment->user_id)
                ->where('id', '!=', $payment->id)
                ->where('status', 'aprobado')
                ->whereNotNull('subscription_type')
                ->exists();

            if (! $hasOtherApproved) {
                Subscription::where('user_id', $payment->user_id)
                    ->update(['status' => Subscription::STATUS_CANCELLED]);
            }

            app(SubscriptionAccessService::class)->sync($payment->user_id);
        } elseif ($payment->book_id) {
            app(BookOrderService::class)->cancelForPayment($payment);

            $book = Book::find($payment->book_id);
            if ($book && $book->isPaid() && ! $book->hasUnlimitedStock()) {
                $book->increment('stock');
            }
        }

        return $payment;
    }

    public function setEnRevision(Payment $payment)
    {
        $payment->status = 'en_revision';
        $payment->save();

        return $payment;
    }
}
