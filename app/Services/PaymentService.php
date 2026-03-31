<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Repositories\EnrollmentRepository;
use App\Repositories\PaymentRepository;
use Carbon\Carbon;

class PaymentService
{
    public function __construct(protected PaymentRepository $repo, protected EnrollmentRepository $enrollmentRepo)
    {
    }

    public function list($perPage = 15, $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function approve(Payment $payment)
    {
        $payment->status = 'aprobado';
        $payment->save();

        $enrollment = $this->enrollmentRepo->create([
            'user_id' => $payment->user_id,
            'course_id' => $payment->course_id,
            'enrolled_at' => Carbon::now(),
            'passed' => false,
        ]);

        return compact('payment', 'enrollment');
    }

    public function reject(Payment $payment)
    {
        $payment->status = 'rechazado';
        $payment->save();

        return $payment;
    }
}
