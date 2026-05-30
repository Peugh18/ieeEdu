<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\PaymentRejectedNotification;
use App\Repositories\EnrollmentRepository;
use App\Repositories\PaymentRepository;

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
        $payment->status = 'aprobado';
        $payment->save();

        if ($payment->course_id) {
            $this->enrollmentService->enroll($payment->user, $payment->course, $payment->id);
        } elseif ($payment->subscription_type) {
            $months = match ($payment->subscription_type) {
                'trimestral' => 3,
                'semestral' => 6,
                'anual' => 12,
                default => 1,
            };

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

        return $payment;
    }

    public function reject(Payment $payment)
    {
        $payment->status = 'rechazado';
        $payment->save();

        $payment->user->notify(new PaymentRejectedNotification($payment));

        return $payment;
    }

    public function setEnRevision(Payment $payment)
    {
        $payment->status = 'en_revision';
        $payment->save();

        return $payment;
    }
}
