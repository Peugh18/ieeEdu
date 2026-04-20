<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicPublicationController extends Controller
{
    public function index()
    {
        $banner = \App\Models\Banner::where('section', 'publicaciones')->orderBy('order')->first();
        return Inertia::render('Publications', [
            'books' => Book::latest()->get(),
            'articles' => Article::latest()->get(),
            'banner' => $banner,
        ]);
    }
}
