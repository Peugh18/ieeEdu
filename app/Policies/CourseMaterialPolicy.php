<?php

namespace App\Policies;

use App\Models\CourseMaterial;
use App\Models\User;

class CourseMaterialPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, CourseMaterial $material): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, CourseMaterial $material): bool
    {
        return $user->role === 'admin';
    }
}
