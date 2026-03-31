<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        return response()->json($course->modules()->get());
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $module = $course->modules()->create(array_merge($validated, ['sort_order' => $course->modules()->count() + 1]));

        return response()->json($module, 201);
    }

    public function update(Request $request, CourseModule $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $module->update($validated);

        return response()->json($module);
    }

    public function destroy(CourseModule $module)
    {
        $module->delete();

        return response()->json(['message' => 'Modulo eliminado']);
    }

    public function reorder(Request $request, Course $course)
    {
        $validated = $request->validate(['order' => 'required|array']);

        foreach ($validated['order'] as $index => $moduleId) {
            CourseModule::where('id', $moduleId)->where('course_id', $course->id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Orden actualizado']);
    }
}
