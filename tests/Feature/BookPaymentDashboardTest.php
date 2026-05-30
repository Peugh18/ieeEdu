<?php

use App\Models\Book;
use App\Models\BookOrder;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('approved book payment reflects in admin dashboard stats', function () {
    Cache::forget('admin_dashboard_stats');

    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate([
        'title' => 'Libro Venta',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 79.90,
        'stock' => 10,
        'is_available' => true,
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'book_id' => $book->id,
        'amount' => 79.90,
        'status' => 'pendiente',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.payments.approve', $payment->id))
        ->assertRedirect();

    expect($payment->fresh()->status)->toBe('aprobado');
    expect(BookOrder::where('payment_id', $payment->id)->exists())->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('stats.bookIncome', 79.9)
            ->where('stats.bookSalesCount', 1)
            ->where('topBooks.0.approved_sales_count', 1)
        );
});

test('admin can register book payment via store and it appears in books stats', function () {
    Cache::forget('admin_dashboard_stats');

    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate([
        'title' => 'Manual BIM',
        'author' => 'IEE',
        'category' => 'Guía',
        'price' => 49.90,
        'stock' => 5,
        'is_available' => true,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.payments.store'), [
            'user_id' => $student->id,
            'product_type' => 'book',
            'book_id' => $book->id,
            'amount' => 49.90,
            'status' => 'aprobado',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(Payment::where('book_id', $book->id)->where('status', 'aprobado')->exists())->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.books.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('stats.book_income', 49.9)
            ->where('stats.book_sales', 1)
            ->where('books.data.0.approved_sales_count', 1)
        );
});
