<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function download(User $user, Certificate $certificate): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->id !== $certificate->user_id) {
            return false;
        }

        $certificateService = app(\App\Services\CertificateService::class);
        return $certificate->course ? $certificateService->checkEligibility($user, $certificate->course) : false;
    }
}
