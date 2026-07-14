<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Banner;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicPublicationController extends Controller
{
    public function index(Request $request)
    {
        $banner = Banner::where('section', 'publicaciones')->orderBy('order')->first();

        $search = $request->input('search');

        $booksQuery = Book::latest();
        if ($search) {
            $booksQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $books = $booksQuery->paginate(6, ['*'], 'books_page')->withQueryString();
        $books->getCollection()->transform(fn ($b) => [
            'id' => $b->id,
            'title' => $b->title,
            'price' => $b->price,
            'sale_price' => $b->sale_price,
            'stock' => $b->stock,
            'author' => $b->author ?? 'Instituto IEE',
            'description' => $b->description,
            'cover_image' => $b->cover_image,
            'download_url' => $b->download_url,
            'is_available' => (bool) $b->is_available,
            'can_purchase' => $b->isPaid() && $b->canAcceptNewPurchase(),
        ]);

        $articlesQuery = Article::latest();
        if ($search) {
            $articlesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('media', 'like', "%{$search}%");
            });
        }

        $articles = $articlesQuery->paginate(6, ['*'], 'articles_page')->withQueryString();
        $articles->getCollection()->transform(fn ($a) => [
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
