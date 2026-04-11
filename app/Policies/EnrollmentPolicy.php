<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determina si el usuario puede ver su progreso y certificados.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->id === $enrollment->user_id || $user->role === 'admin';
    }

    /**
     * Determina si el usuario puede descargar el certificado.
     */
    public function downloadCertificate(User $user, Enrollment $enrollment): bool
    {
        return ($user->id === $enrollment->user_id && $enrollment->completed_at !== null) 
               || $user->role === 'admin';
    }
}
