<?php

use App\Models\Book;
use App\Models\BookDownload;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('download book records download event', function () {
    Storage::fake('public');
    Storage::disk('public')->put('books/test.pdf', 'dummy content');

    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate([
        'title' => 'Libro Test',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 0,
        'is_available' => true,
        'file_path' => 'books/test.pdf',
    ]);

    $this->actingAs($student)
        ->get(route('student.publications.books.download', $book))
        ->assertSuccessful();

    expect(BookDownload::where('book_id', $book->id)->count())->toBe(1);
    expect(BookDownload::first()->source)->toBe('file');
    expect(BookDownload::first()->user_id)->toBe($student->id);
});

test('external book download records and redirects', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate([
        'title' => 'Libro Externo',
        'author' => 'IEE',
        'category' => 'Guía',
        'price' => 0,
        'is_available' => true,
        'download_url' => 'https://example.com/libro.pdf',
    ]);

    $this->actingAs($student)
        ->get(route('student.publications.books.download', $book))
        ->assertRedirect('https://example.com/libro.pdf');

    expect(BookDownload::where('book_id', $book->id)->where('source', 'external')->exists())->toBeTrue();
});

test('book purchase interest records whatsapp lead', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate([
        'title' => 'Libro Pago',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 49.90,
        'is_available' => true,
    ]);

    $response = $this->actingAs($student)
        ->get(route('student.publications.books.interest', $book));

    $response->assertRedirect();
    expect(str_starts_with($response->headers->get('Location'), 'https://wa.me/'))->toBeTrue();
    expect(BookDownload::where('book_id', $book->id)->where('source', 'whatsapp')->exists())->toBeTrue();
});

test('admin books index includes download counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $book = Book::forceCreate([
        'title' => 'Conteo',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 0,
        'is_available' => true,
    ]);

    BookDownload::create([
        'book_id' => $book->id,
        'user_id' => $admin->id,
        'source' => 'file',
        'created_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.books.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('stats.downloads', 1)
            ->where('books.data.0.downloads_count', 1)
        );
});
