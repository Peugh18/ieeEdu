<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:course_modules,id',
            'content_type' => 'required|in:video,live,event,text',
            'video_url' => 'required_if:content_type,video|nullable|url',
            'live_link' => 'required_if:content_type,live|nullable|url',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $course = $this->route('course');
            $isStore = true;

            if (! $course) {
                $lesson = $this->route('lesson');
                $course = $lesson?->course;
                $isStore = false;
            }

            if ($course && $course->type !== 'evento' && empty($this->input('module_id'))) {
                $message = $isStore
                    ? 'Debes crear un módulo antes de agregar clases.'
                    : 'Debes asignar la clase a un módulo.';
                $validator->errors()->add('module_id', $message);
            }
        });
    }
}
