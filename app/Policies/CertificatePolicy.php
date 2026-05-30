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
        return $user->role === 'admin' || $user->id === $certificate->user_id;
    }
}
