<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMaterialRequest;
use App\Http\Requests\Admin\UpdateMaterialRequest;
use App\Models\CourseLesson;
use App\Models\CourseMaterial;

class MaterialController extends Controller
{
    public function index(CourseLesson $lesson)
    {
        return response()->json($lesson->materials()->get());
    }

    public function store(StoreMaterialRequest $request, CourseLesson $lesson)
    {
        $this->authorize('create', CourseMaterial::class);
        $validated = $request->validated();
        $validated['file_path'] = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            $kind = in_array($ext, ['pdf'], true) ? 'pdf' : (in_array($ext, ['xls', 'xlsx', 'csv'], true) ? 'excel' : 'other');
            $path = $file->store('materials', 'public');
            $validated['kind'] = $kind;
            $validated['file_path'] = '/storage/'.$path;
            $validated['external_url'] = null;
        }

        $material = $lesson->materials()->create($validated);

        return response()->json($material, 201);
    }

    public function update(UpdateMaterialRequest $request, CourseMaterial $material)
    {
        $this->authorize('update', $material);
        $validated = $request->validated();

        if (($validated['kind'] ?? null) === 'url' || $request->filled('external_url')) {
            $validated['file_path'] = null;
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            $kind = in_array($ext, ['pdf'], true) ? 'pdf' : (in_array($ext, ['xls', 'xlsx', 'csv'], true) ? 'excel' : 'other');
            $path = $file->store('materials', 'public');
            $validated['kind'] = $kind;
            $validated['file_path'] = '/storage/'.$path;
            $validated['external_url'] = null;
        }

        $material->update($validated);

        return response()->json($material);
    }

    public function destroy(CourseMaterial $material)
    {
        $this->authorize('delete', $material);
        $material->delete();

        return response()->json(['message' => 'Material eliminado']);
    }
}
