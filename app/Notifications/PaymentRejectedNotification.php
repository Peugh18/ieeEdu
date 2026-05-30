<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Actualización sobre tu pago')
            ->greeting('Hola '.$notifiable->name.',')
            ->line('Hubo un inconveniente con la validación de tu pago por S/ '.number_format($this->payment->amount, 2).' para el curso: '.$this->payment->course->title.'.')
            ->line('El pago ha sido rechazado. Por favor, verifica el comprobante e intenta subirlo nuevamente.')
            ->action('Ver mis pagos', route('student.payments.index'))
            ->line('Si tienes dudas, por favor contáctanos.');
    }
}
