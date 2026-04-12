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
            'file_path' => 'nullable|file|mimes:pdf|max:10000',
            'download_url' => 'required|string|max:255',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('articles/pdfs', 'public');
            $data['download_url'] = asset('storage/' . $data['file_path']);
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
            'file_path' => 'nullable|file|mimes:pdf|max:10000',
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

        if ($request->hasFile('file_path')) {
            if ($article->file_path) {
                Storage::disk('public')->delete($article->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('articles/pdfs', 'public');
            $data['download_url'] = asset('storage/' . $data['file_path']);
        } else {
            unset($data['file_path']);
        }

        $article->update($data);

        return redirect()->back()->with('success', 'Artículo actualizado correctamente.');
    }

    public function destroy(Article $article)
    {
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        if ($article->file_path) {
            Storage::disk('public')->delete($article->file_path);
        }
        $article->delete();

        return redirect()->back()->with('success', 'Artículo eliminado correctamente.');
    }
}
