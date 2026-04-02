<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('courses');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50])) $perPage = 20;

        $categories = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return Inertia::render('admin/Categories', [
            'categories' => $categories,
            'filters'    => $request->only(['search', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $slug = Str::slug($data['name']);
        
        // Ensure slug uniqueness
        $count = Category::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $category = Category::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        if ($request->wantsJson()) {
            return response()->json($category);
        }

        return redirect()->back()->with('success', 'Categoría creada con éxito.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->name = $data['name'];
        $category->slug = Str::slug($data['name']);
        
        // Ensure slug uniqueness
        $count = Category::where('slug', 'like', $category->slug . '%')
            ->where('id', '!=', $category->id)
            ->count();
        if ($count > 0) {
            $category->slug .= '-' . ($count + 1);
        }

        $category->save();

        return redirect()->back()->with('success', 'Categoría actualizada con éxito.');
    }

    public function destroy(Category $category)
    {
        if ($category->courses()->exists()) {
            return redirect()->back()->with('error', 'Esta categoría tiene cursos asignados y no se puede eliminar.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Categoría eliminada con éxito.');
    }
}
