<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    public function index(Request $request)
    {
        $users = $this->service->list(15, $request->only('role', 'status', 'search'));

        return Inertia::render('admin/Users', [
            'users' => $users,
            'filters' => $request->only('role', 'status', 'search'),
        ]);
    }

    public function store(UserRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->back()->with('success', 'Usuario creado.');
    }

    public function update(UserRequest $request, User $user)
    {
        $this->service->update($user, $request->validated());

        return redirect()->back()->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return redirect()->back()->with('success', 'Usuario eliminado.');
    }
}
