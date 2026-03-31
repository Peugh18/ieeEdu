<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        return response()->json($course->lessons()->get());
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:course_modules,id',
            'content_type' => 'required|in:video,live,event,text',
            'video_url' => 'required_if:content_type,video|nullable|url',
            'live_link' => 'required_if:content_type,live|nullable|url',
            // Horario opcional (para simplificar). Si viene, se valida.
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        // Reglas por tipo de curso
        // Grabado / En vivo requieren que la clase esté dentro de un módulo
        if ($course->type !== 'evento' && empty($validated['module_id'])) {
            return response()->json(['message' => 'Debes crear un módulo antes de agregar clases.'], 422);
        }

        $lesson = $course->lessons()->create(array_merge($validated, ['sort_order' => $course->lessons()->count() + 1]));

        return response()->json($lesson, 201);
    }

    public function update(Request $request, CourseLesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:course_modules,id',
            'content_type' => 'required|in:video,live,event,text',
            'video_url' => 'required_if:content_type,video|nullable|url',
            'live_link' => 'required_if:content_type,live|nullable|url',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $lesson->loadMissing('course');
        if ($lesson->course && $lesson->course->type !== 'evento' && empty($validated['module_id'])) {
            return response()->json(['message' => 'Debes asignar la clase a un módulo.'], 422);
        }

        $lesson->update($validated);

        return response()->json($lesson);
    }

    public function destroy(CourseLesson $lesson)
    {
        $lesson->delete();

        return response()->json(['message' => 'Clase eliminada']);
    }

    public function reorder(Request $request, Course $course)
    {
        $validated = $request->validate([ 'order' => 'required|array' ]);

        foreach ($validated['order'] as $index => $lessonId) {
            CourseLesson::where('id', $lessonId)->where('course_id', $course->id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Orden de clases actualizado']);
    }
}
