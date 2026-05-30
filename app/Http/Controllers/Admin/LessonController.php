<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLessonRequest;
use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        return response()->json($course->lessons()->get());
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        $validated = $request->validated();

        $lesson = $course->lessons()->create(array_merge($validated, ['sort_order' => $course->lessons()->count() + 1]));

        return response()->json($lesson, 201);
    }

    public function update(StoreLessonRequest $request, CourseLesson $lesson)
    {
        $validated = $request->validated();

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
        $validated = $request->validate(['order' => 'required|array']);

        foreach ($validated['order'] as $index => $lessonId) {
            CourseLesson::where('id', $lessonId)->where('course_id', $course->id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Orden de clases actualizado']);
    }
}
