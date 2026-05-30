<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        if ($this->has('password') && $this->filled('password')) {
            return [
                'password' => 'required|string|min:8|confirmed',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
