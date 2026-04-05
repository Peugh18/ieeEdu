<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Repositories\EnrollmentRepository;
use App\Repositories\PaymentRepository;
use Carbon\Carbon;

class PaymentService
{
    public function __construct(
        protected PaymentRepository $repo, 
        protected EnrollmentRepository $enrollmentRepo,
        protected EnrollmentService $enrollmentService
    ) {
    }

    public function list($perPage = 15, $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function approve(Payment $payment)
    {
        $payment->status = 'aprobado';
        $payment->save();

        $this->enrollmentService->enroll($payment->user, $payment->course, $payment->id);

        return $payment;
    }

    public function reject(Payment $payment)
    {
        $payment->status = 'rechazado';
        $payment->save();
        return $payment;
    }

    public function setEnRevision(Payment $payment)
    {
        $payment->status = 'en_revision';
        $payment->save();
        return $payment;
    }
}
