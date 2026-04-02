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
        return Inertia::render('Publications', [
            'books' => Book::latest()->get(),
            'articles' => Article::latest()->get(),
        ]);
    }
}
