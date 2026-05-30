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
        $message = (new MailMessage)
            ->subject('Actualización sobre tu pago')
            ->greeting('Hola '.$notifiable->name.',')
            ->line('Tu pago por S/ '.number_format($this->payment->amount, 2).' ha sido rechazado.');

        if ($this->payment->course) {
            $message->line('Curso: '.$this->payment->course->title)
                ->line('Verifica el comprobante e intenta subirlo nuevamente.');
        } elseif ($this->payment->subscription_type) {
            $message->line('Membresía: '.ucfirst($this->payment->subscription_type))
                ->line('Verifica el comprobante de tu plan Premium e intenta nuevamente.');
        }

        return $message
            ->action('Ver mis pagos', route('student.payments.index'))
            ->line('Si tienes dudas, contáctanos.');
    }
}
