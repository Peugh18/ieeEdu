<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(protected UserRepository $repo)
    {
    }

    public function list($perPage = 15, $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->repo->create($data);
    }

    public function update(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->repo->update($user, $data);
    }

    public function delete(User $user)
    {
        return $this->repo->delete($user);
    }
}
