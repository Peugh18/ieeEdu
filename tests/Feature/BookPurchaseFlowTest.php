<?php

use App\Models\Book;
use App\Models\BookOrder;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;

test('subscription purchase intent creates pending payment and whatsapp redirect flash', function () {
    $student = User::factory()->create(['role' => 'usuario']);

    $response = $this->actingAs($student)->post(route('student.purchase-intent.store'), [
        'plan' => 'anual',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('open_whatsapp');
    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'subscription_type' => 'anual',
        'status' => 'pendiente',
        'amount' => 990,
    ]);
});

test('purchase intent creates pending payment and whatsapp redirect flash', function () {
    $student = User::factory()->create(['role' => 'usuario', 'telefono' => '999888777']);
    $book = Book::forceCreate([
        'title' => 'Libro WA',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 45,
        'stock' => 3,
        'is_available' => true,
    ]);

    $response = $this->actingAs($student)->post(route('student.purchase-intent.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('open_whatsapp');
    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'book_id' => $book->id,
        'status' => 'pendiente',
    ]);
});

test('course purchase intent creates pending payment', function () {
    $student = User::factory()->create(['role' => 'usuario']);
    $course = Course::factory()->create(['price' => 120, 'status' => 'PUBLICADO', 'type' => 'grabado']);

    $this->actingAs($student)->post(route('student.purchase-intent.store'), [
        'course_id' => $course->id,
    ])->assertRedirect()->assertSessionHas('open_whatsapp');

    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pendiente',
    ]);
});

test('approving book payment creates book order for shipping', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate([
        'title' => 'Envío Test',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 60,
        'stock' => 2,
        'is_available' => true,
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'book_id' => $book->id,
        'amount' => 60,
        'status' => 'en_revision',
    ]);

    $this->actingAs($admin)->patch(route('admin.payments.approve', $payment->id))->assertRedirect();

    expect(BookOrder::where('payment_id', $payment->id)->exists())->toBeTrue();
    expect(BookOrder::first()->shipping_status)->toBe(BookOrder::STATUS_AWAITING_ADDRESS);
});

test('admin can update book order shipping with tracking url', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'usuario']);
    $book = Book::forceCreate([
        'title' => 'Courier',
        'author' => 'IEE',
        'category' => 'Libro',
        'price' => 50,
        'stock' => 1,
        'is_available' => true,
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'book_id' => $book->id,
        'amount' => 50,
        'status' => 'aprobado',
    ]);

    $order = BookOrder::create([
        'payment_id' => $payment->id,
        'book_id' => $book->id,
        'user_id' => $student->id,
        'shipping_status' => BookOrder::STATUS_AWAITING_ADDRESS,
    ]);

    $this->actingAs($admin)->patch(route('admin.book-orders.update', $order), [
        'shipping_status' => BookOrder::STATUS_SHIPPED,
        'shipping_address' => 'Av. Grau 123',
        'district' => 'Trujillo',
        'province' => 'Trujillo',
        'department' => 'La Libertad',
        'shipping_phone' => '999111222',
        'delivery_mode' => 'home',
        'carrier' => 'Olva Courier',
        'tracking_url' => 'https://olvacourier.com/tracking/ABC123',
        'student_note' => 'Llegará en 3-5 días hábiles',
    ])->assertRedirect();

    $order->refresh();
    expect($order->shipping_status)->toBe(BookOrder::STATUS_SHIPPED);
    expect($order->tracking_url)->toContain('olvacourier.com');
});
