<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function index(CourseLesson $lesson)
    {
        return response()->json($lesson->materials()->get());
    }

    public function store(Request $request, CourseLesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'kind' => 'required|in:pdf,excel,url,other',
            'file_path' => 'nullable|string',
            'external_url' => 'nullable|url',
            'file' => 'nullable|file|max:20480',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            $kind = in_array($ext, ['pdf'], true) ? 'pdf' : (in_array($ext, ['xls', 'xlsx', 'csv'], true) ? 'excel' : 'other');
            $path = $file->store('materials', 'public');
            $validated['kind'] = $kind;
            $validated['file_path'] = '/storage/' . $path;
            $validated['external_url'] = null;
        }

        $material = $lesson->materials()->create($validated);

        return response()->json($material, 201);
    }

    public function update(Request $request, CourseMaterial $material)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'kind' => 'required|in:pdf,excel,url,other',
            'file_path' => 'nullable|string',
            'external_url' => 'nullable|url',
            'file' => 'nullable|file|max:20480',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            $kind = in_array($ext, ['pdf'], true) ? 'pdf' : (in_array($ext, ['xls', 'xlsx', 'csv'], true) ? 'excel' : 'other');
            $path = $file->store('materials', 'public');
            $validated['kind'] = $kind;
            $validated['file_path'] = '/storage/' . $path;
            $validated['external_url'] = null;
        }

        $material->update($validated);

        return response()->json($material);
    }

    public function destroy(CourseMaterial $material)
    {
        $material->delete();
        return response()->json(['message' => 'Material eliminado']);
    }
}
