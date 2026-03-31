<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function paginate($perPage = 15, $filters = [])
    {
        $query = User::query();

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$filters['search']}%")
                ->orWhere('email', 'like', "%{$filters['search']}%"));
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        $user->update($data);

        return $user;
    }

    public function delete(User $user)
    {
        return $user->delete();
    }
}
