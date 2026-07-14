<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StorePurchaseIntentRequest;
use App\Models\Book;
use App\Models\BookOrder;
use App\Models\Course;
use App\Models\Payment;
use App\Support\PlanPricing;
use App\Support\WhatsAppPurchaseMessage;
use Illuminate\Support\Facades\Auth;

class PurchaseIntentController extends Controller
{
    public function store(StorePurchaseIntentRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if (! empty($data['plan'])) {
            $plan = $data['plan'];
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_type' => $plan,
                'amount' => PlanPricing::price($plan),
                'status' => 'pendiente',
            ]);

            $whatsappUrl = WhatsAppPurchaseMessage::subscription(
                $user,
                PlanPricing::name($plan),
                $payment,
            );
        } elseif (! empty($data['book_id'])) {
            $book = Book::findOrFail($data['book_id']);
            $payment = Payment::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'amount' => (float) $book->price,
                'status' => 'pendiente',
            ]);

            // Create BookOrder immediately with prefilled shipping details
            BookOrder::create([
                'payment_id' => $payment->id,
                'book_id' => $book->id,
                'user_id' => $user->id,
                'shipping_status' => BookOrder::STATUS_AWAITING_ADDRESS,
                'department' => $data['department'] ?? null,
                'province' => $data['province'] ?? null,
                'district' => $data['district'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
                'shipping_phone' => $data['shipping_phone'] ?? null,
            ]);

            $whatsappUrl = WhatsAppPurchaseMessage::book($user, $book, $payment, $data);
        } else {
            $course = Course::findOrFail($data['course_id']);
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->effectivePrice(),
                'status' => 'pendiente',
            ]);

            $whatsappUrl = WhatsAppPurchaseMessage::course($user, $course, $payment);
        }

        return back()->with([
            'success' => 'Solicitud registrada. Coordina tu pago por WhatsApp y luego sube tu comprobante en Mis Pagos.',
            'open_whatsapp' => $whatsappUrl,
        ]);
    }
}
