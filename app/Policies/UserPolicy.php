<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    public function toggleStatus(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
