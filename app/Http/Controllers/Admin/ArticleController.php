<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/Articles', [
            'articles' => Article::latest()->paginate(12),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'media' => 'required|string|max:255',
            'published_at' => 'required|date',
            'thumbnail' => 'required|image|max:2048',
            'download_url' => 'required|string|max:255',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->back()->with('success', 'Artículo creado correctamente.');
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'media' => 'required|string|max:255',
            'published_at' => 'required|date',
            'thumbnail' => 'nullable|image|max:2048',
            'download_url' => 'required|string|max:255',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $article->update($data);

        return redirect()->back()->with('success', 'Artículo actualizado correctamente.');
    }

    public function destroy(Article $article)
    {
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        $article->delete();

        return redirect()->back()->with('success', 'Artículo eliminado correctamente.');
    }
}
