<?php

namespace App\Support;

use App\Models\Book;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use App\Services\SiteSettingsService;

class WhatsAppPurchaseMessage
{
    public static function course(User $user, Course $course, Payment $payment): string
    {
        $message = sprintf(
            "Hola, quiero inscribirme al curso:\n*%s*\nMonto: S/ %s\n\nMis datos:\n• Nombre: %s\n• Email: %s\n• Solicitud: #%d\n\nQuedo atento a los datos de pago. Luego subiré mi comprobante en la plataforma.",
            $course->title,
            number_format((float) $payment->amount, 2),
            $user->name,
            $user->email,
            $payment->id,
        );

        return self::url($message);
    }

    public static function subscription(User $user, string $planName, Payment $payment): string
    {
        $message = sprintf(
            "Hola, quiero contratar la membresía Premium:\n*%s*\nMonto: S/ %s\n\nMis datos:\n• Nombre: %s\n• Email: %s\n• Solicitud: #%d\n\nQuedo atento a los datos de pago (Yape/BCP). Luego subiré mi comprobante en la plataforma.",
            $planName,
            number_format((float) $payment->amount, 2),
            $user->name,
            $user->email,
            $payment->id,
        );

        return self::url($message);
    }

    public static function book(User $user, Book $book, Payment $payment): string
    {
        $message = sprintf(
            "Hola, quiero comprar el libro:\n*%s*\nMonto: S/ %s\n\nMis datos:\n• Nombre: %s\n• Email: %s\n• Teléfono: %s\n• Solicitud: #%d\n\nIndíquenme datos de pago y confirmaré mi dirección de envío en Perú. Luego subiré mi comprobante en la plataforma.",
            $book->title,
            number_format((float) $payment->amount, 2),
            $user->name,
            $user->email,
            $user->telefono ?: 'No registrado',
            $payment->id,
        );

        return self::url($message);
    }

    private static function url(string $message): string
    {
        $number = SiteSettingsService::whatsappSales();

        return 'https://wa.me/'.$number.'?text='.urlencode($message);
    }
}
