<?php

use App\Models\Article;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('explorePublications NO incluye file_path en JSON', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    Book::forceCreate(['title' => 'B1', 'author' => 'A1', 'category' => 'Libro', 'price' => 0, 'is_available' => true, 'file_path' => 'books/secret.pdf']);
    Article::forceCreate(['title' => 'A1', 'media' => 'Análisis', 'published_at' => now(), 'file_path' => 'articles/secret.pdf']);

    $response = $this->actingAs($student)->get(route('student.explore.publications'));

    $response->assertSuccessful();

    $content = $response->getContent();
    expect($content)->not->toContain('"file_path":"books\/secret.pdf"');
    expect($content)->not->toContain('"file_path":"articles\/secret.pdf"');
});

test('download book 403 si no disponible', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate(['title' => 'B2', 'author' => 'A1', 'category' => 'Libro', 'price' => 0, 'is_available' => false, 'file_path' => 'books/test.pdf']);

    $response = $this->actingAs($student)->get(route('student.publications.books.download', $book));
    $response->assertForbidden();
});

test('download book 404 si no hay archivo fisico', function () {
    Storage::fake('public');
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate(['title' => 'B3', 'author' => 'A1', 'category' => 'Libro', 'price' => 0, 'is_available' => true, 'file_path' => 'books/test.pdf']);

    $response = $this->actingAs($student)->get(route('student.publications.books.download', $book));
    $response->assertNotFound();
});

test('guest cannot download book', function () {
    $book = Book::forceCreate([
        'title' => 'Guest Book',
        'slug' => 'guest-book',
        'file_path' => 'private/books/test.pdf',
        'is_available' => true,
        'price' => 0,
        'category' => 'Libro',
        'author' => 'Author',
    ]);

    $response = $this->get(route('student.publications.books.download', $book->id));

    $response->assertRedirect('/login');
});

test('guest cannot download article', function () {
    $article = Article::forceCreate([
        'title' => 'Guest Article',
        'slug' => 'guest-article',
        'file_path' => 'private/articles/test.pdf',
        'published_at' => now(),
        'media' => 'Media',
    ]);

    $response = $this->get(route('student.publications.articles.download', $article->id));

    $response->assertRedirect('/login');
});

test('download book OK si disponible y archivo existe', function () {
    Storage::fake('public');
    Storage::disk('public')->put('books/test.pdf', 'dummy content');
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate(['title' => 'B4', 'author' => 'A1', 'category' => 'Libro', 'price' => 0, 'is_available' => true, 'file_path' => 'books/test.pdf']);

    $response = $this->actingAs($student)->get(route('student.publications.books.download', $book));
    $response->assertSuccessful();
    $response->assertDownload('test.pdf');
});
