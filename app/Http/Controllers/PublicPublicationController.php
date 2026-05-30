<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Banner;
use App\Models\Book;
use Inertia\Inertia;

class PublicPublicationController extends Controller
{
    public function index()
    {
        $banner = Banner::where('section', 'publicaciones')->orderBy('order')->first();

        $books = Book::latest()->get()->map(fn ($b) => [
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

        $articles = Article::latest()->get()->map(fn ($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'media' => $a->media ?? 'Análisis',
            'published_at' => $a->published_at ? $a->published_at->format('Y-m-d') : now()->format('Y-m-d'),
            'thumbnail' => $a->thumbnail,
            'download_url' => $a->download_url,
        ]);

        return Inertia::render('Publications', [
            'books' => $books,
            'articles' => $articles,
            'banner' => $banner,
        ]);
    }
}
