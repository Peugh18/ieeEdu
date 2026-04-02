<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|alpha_dash|unique:courses,slug,' . ($this->course?->id ?? 'null'),
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            // sale_price is computed by the backend from price+discount, just validate its type if sent
            'sale_price' => 'nullable|numeric|min:0',
            // Legacy (ya no lo usaremos en UI, pero lo dejamos por compatibilidad)
            'discount' => 'nullable|numeric|min:0|max:100',
            'duration_weeks' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            // Keep "evento" for backwards compatibility with existing DB enum (masterclass maps to evento)
            'type' => 'required|in:grabado,en vivo,masterclass,evento',
            'status' => 'sometimes|in:BORRADOR,PUBLICADO,OCULTO',
            'category_id' => 'nullable|exists:categories,id',
            'docente_id' => 'nullable|exists:users,id',
            // Image can be either an uploaded file (new) or an existing stored URL/path (update)
            'image' => 'nullable',
            'image_file' => ($this->isMethod('post') ? 'required' : 'nullable') . '|file|mimes:jpg,jpeg,png,webp|max:5120',
            'certificate_enabled' => 'sometimes|boolean',
            'instructor_name' => 'nullable|string|max:255',
            'instructor_title' => 'nullable|string|max:255',
            'instructor_bio' => 'nullable|string',
            'instructor_image' => 'nullable|string',
            'instructor_image_file' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'class_hours' => 'nullable|integer|min:0',
            'whatsapp_link' => 'nullable|string|url',
            'objectives' => 'nullable|string',
            'requirements' => 'nullable|string',
        ];
    }
}
