<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('public publications catalog does not expose file_path in json', function () {
    // Usamos forceCreate dado que podría no haber factories configurados para ciertas relaciones (e.g., admin_id)
    Book::forceCreate([
        'title' => 'Libro Público de Prueba',
        'slug' => 'libro-publico-de-prueba',
        'file_path' => 'private/books/secret.pdf',
        'is_available' => true,
        'price' => 0,
        'category' => 'Libro',
        'author' => 'Autor Público',
    ]);

    Article::forceCreate([
        'title' => 'Artículo Público de Prueba',
        'slug' => 'articulo-publico-de-prueba',
        'file_path' => 'private/articles/secret.pdf',
        'published_at' => now(),
        'media' => 'Análisis',
    ]);

    $response = $this->get('/publicaciones');

    $response->assertSuccessful();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Publications')
        ->has('books.data', 1)
        ->has('articles.data', 1)
        ->missing('books.data.0.file_path')
        ->missing('articles.data.0.file_path')
        ->has('books.data.0.id')
        ->has('books.data.0.title')
        ->has('articles.data.0.id')
        ->has('articles.data.0.title')
    );
});
