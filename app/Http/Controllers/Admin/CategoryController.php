<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service)
    {
    }

    public function index(Request $request)
    {
        $categories = $this->service->list(15);

        return Inertia::render('admin/Categories', [
            'categories' => $categories,
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $category = $this->service->create($request->validated());

        if ($request->wantsJson()) {
            return response()->json($category, 201);
        }

        return redirect()->back()->with('success', 'Categoría creada.');
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $this->service->update($category, $request->validated());

        return redirect()->back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        $this->service->delete($category);

        return redirect()->back()->with('success', 'Categoría eliminada.');
    }
}
