<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:0',
            'max_attempts' => 'required|integer|min:1',
            'minimum_score' => 'required|numeric|min:0|max:100',
            'randomize_questions' => 'boolean',
        ];
    }
}
