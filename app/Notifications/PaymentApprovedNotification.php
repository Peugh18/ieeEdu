<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('¡Pago Aprobado!')
            ->greeting('Hola '.$notifiable->name.',')
            ->line('Tu pago por el monto de S/ '.number_format($this->payment->amount, 2).' ha sido aprobado.');

        if ($this->payment->course) {
            $message->line('Ya tienes acceso al curso: '.$this->payment->course->title)
                ->action('Ir al Aula Virtual', route('student.classroom', $this->payment->course->slug));
        } elseif ($this->payment->book) {
            $message->line('Tu pedido del libro "'.$this->payment->book->title.'" fue aprobado. Pronto confirmaremos el envío a tu dirección.')
                ->action('Ver mis compras', route('student.payments.index'));
        } else {
            $message->line('Ya tienes acceso a los beneficios de tu membresía.')
                ->action('Ir a mi Dashboard', route('dashboard'));
        }

        return $message->line('Gracias por confiar en el Instituto IEE.');
    }
}
