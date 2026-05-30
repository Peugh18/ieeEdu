<?php

use App\Models\Book;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

function createPaidBook(array $overrides = []): Book
{
    return Book::forceCreate(array_merge([
        'title' => 'Libro Premium',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 59.90,
        'stock' => 5,
        'is_available' => true,
        'file_path' => 'books/test.pdf',
    ], $overrides));
}

test('student can create payment for paid book', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $book = createPaidBook();

    $response = $this->actingAs($student)->post(route('student.payments.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertRedirect(route('student.payments.index'));
    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'book_id' => $book->id,
        'status' => 'pendiente',
        'amount' => 59.90,
    ]);
});

test('student cannot download paid book without approved payment', function () {
    Storage::fake('public');
    Storage::disk('public')->put('books/test.pdf', 'content');

    $student = User::factory()->create(['role' => 'usuario']);
    $book = createPaidBook();

    $this->actingAs($student)
        ->get(route('student.publications.books.download', $book))
        ->assertForbidden();
});

test('student can download paid book after payment approval and stock decreases', function () {
    Storage::fake('public');
    Storage::disk('public')->put('books/test.pdf', 'content');
    Cache::forget('admin_dashboard_stats');

    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $book = createPaidBook(['stock' => 2]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'book_id' => $book->id,
        'amount' => 59.90,
        'status' => 'pendiente',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.payments.approve', $payment->id))
        ->assertRedirect();

    expect($book->fresh()->stock)->toBe(1);
    expect($student->fresh()->hasBookAccess($book->id))->toBeTrue();

    $this->actingAs($student)
        ->get(route('student.publications.books.download', $book))
        ->assertForbidden();
});

test('student cannot create book payment when out of stock', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $book = createPaidBook(['stock' => 0]);

    $response = $this->actingAs($student)->post(route('student.payments.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertSessionHasErrors('book_id');
});

test('reserved pending payments reduce available stock', function () {
    $studentA = User::factory()->create(['role' => 'usuario']);
    $studentB = User::factory()->create(['role' => 'usuario']);
    $book = createPaidBook(['stock' => 1]);

    Payment::create([
        'user_id' => $studentA->id,
        'book_id' => $book->id,
        'amount' => 59.90,
        'status' => 'pendiente',
    ]);

    $response = $this->actingAs($studentB)->post(route('student.payments.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertSessionHasErrors('book_id');
});

test('student payments index includes book payments', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $book = createPaidBook();

    Payment::create([
        'user_id' => $student->id,
        'book_id' => $book->id,
        'amount' => 59.90,
        'status' => 'en_revision',
    ]);

    $this->actingAs($student)
        ->get(route('student.payments.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('payments.data.0.book.title', $book->title)
        );
});
