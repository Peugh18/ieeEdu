<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Book;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CatalogController extends Controller
{
    /**
     * Explorar Publicaciones
     */
    public function explorePublications()
    {
        $books = Book::all()->map(fn ($b) => [
            'id' => $b->id,
            'category' => $b->category ?? 'Libro',
            'title' => $b->title,
            'price' => $b->price,
            'author' => $b->author ?? 'Instituto IEE',
            'description' => $b->description,
            'cover_image' => $b->cover_image,
            'download_url' => $b->download_url,
            'is_available' => (bool) $b->is_available,
        ]);

        $articles = Article::all()->map(fn ($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'media' => $a->media ?? 'Análisis',
            'published_at' => $a->published_at ? $a->published_at->format('Y-m-d') : now()->format('Y-m-d'),
            'thumbnail' => $a->thumbnail,
            'download_url' => $a->download_url,
        ]);

        $banner = Banner::where('section', 'publicaciones')->orderBy('order')->first();

        return Inertia::render('Publications', [
            'books' => $books,
            'articles' => $articles,
            'banner' => $banner,
            'isDashboard' => true,
        ]);
    }

    /**
     * Explorar Masterclasses
     */
    public function exploreMasterclasses(Request $request)
    {
        $banner = Banner::where('section', 'masterclass')->first();

        $categories = Category::whereHas('courses', function ($q) {
            $q->where('type', 'evento');
        })->orderBy('name')->get();

        $query = Course::published()
            ->where('type', 'evento')
            ->with(['category']);

        if ($request->filled('category') && $request->category !== 'Todas') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(6)->withQueryString();

        return Inertia::render('Masterclasses', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $request->only(['category']),
            'banner' => $banner,
            'isDashboard' => true,
        ]);
    }

    /**
     * Explorar Consultoría
     */
    public function exploreConsultoria()
    {
        $banner = Banner::where('section', 'consultoria')->orderBy('order')->first();

        return Inertia::render('Consultoria', [
            'banner' => $banner,
            'isDashboard' => true,
        ]);
    }

    /**
     * Descargar Libro
     */
    public function downloadBook(Book $book)
    {
        if (! $book->is_available) {
            abort(403, 'El libro no está disponible para descarga.');
        }

        if (! $book->file_path || ! Storage::disk('public')->exists($book->file_path)) {
            abort(404, 'El archivo del libro no se encuentra disponible.');
        }

        return Storage::disk('public')->download($book->file_path);
    }

    /**
     * Descargar Artículo
     */
    public function downloadArticle(Article $article)
    {
        if (! $article->file_path || ! Storage::disk('public')->exists($article->file_path)) {
            abort(404, 'El archivo del artículo no se encuentra disponible.');
        }

        return Storage::disk('public')->download($article->file_path);
    }
}
