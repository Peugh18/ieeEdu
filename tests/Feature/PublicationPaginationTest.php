<?php

use App\Models\Article;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function createBooks(int $count): void
{
    for ($i = 1; $i <= $count; $i++) {
        Book::forceCreate([
            'title' => 'Libro '.$i,
            'slug' => 'libro-'.$i,
            'price' => 0,
            'is_available' => true,
            'category' => 'Libro',
            'author' => 'Autor',
        ]);
    }
}

function createArticles(int $count): void
{
    for ($i = 1; $i <= $count; $i++) {
        Article::forceCreate([
            'title' => 'Artículo '.$i,
            'slug' => 'articulo-'.$i,
            'published_at' => now(),
            'media' => 'Análisis',
        ]);
    }
}

test('publicaciones pagina libros con 6 por pagina', function () {
    createBooks(10);

    $response = $this->get('/publicaciones');

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Publications')
        ->has('books.data', 6)
        ->has('articles.data', 0)
    );

    $responsePage2 = $this->get('/publicaciones?books_page=2');
    $responsePage2->assertSuccessful();
    $responsePage2->assertInertia(fn (Assert $page) => $page
        ->component('Publications')
        ->has('books.data', 4)
    );
});

test('publicaciones pagina articulos con 6 por pagina', function () {
    createArticles(10);

    $response = $this->get('/publicaciones');

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Publications')
        ->has('articles.data', 6)
    );

    $responsePage2 = $this->get('/publicaciones?articles_page=2');
    $responsePage2->assertSuccessful();
    $responsePage2->assertInertia(fn (Assert $page) => $page
        ->component('Publications')
        ->has('articles.data', 4)
    );
});

test('guest puede acceder a publicaciones', function () {
    createBooks(3);
    createArticles(2);

    $response = $this->get('/publicaciones');

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Publications')
        ->has('books.data', 3)
        ->has('articles.data', 2)
    );
});

test('estudiante puede acceder a explore publications', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    createBooks(3);
    createArticles(2);

    $response = $this->actingAs($student)->get(route('student.explore.publications'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Publications')
        ->has('books.data', 3)
        ->has('articles.data', 2)
        ->where('isDashboard', true)
    );
});
