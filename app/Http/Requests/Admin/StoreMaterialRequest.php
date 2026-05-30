<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'external_url' => $this->filled('external_url') ? $this->input('external_url') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'kind' => 'required_without:file|in:pdf,excel,url,other',
            'external_url' => 'nullable|url|required_without:file',
            'file' => 'nullable|file|mimes:pdf,xls,xlsx,csv,doc,docx,zip,png,jpg,jpeg|max:20480',
        ];
    }
}
