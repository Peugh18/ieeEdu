<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'telefono' => 'nullable|string|max:20',
            'role' => 'required|in:admin,usuario',
            'status' => 'required|in:activo,inactivo',
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
        ];
    }
}
