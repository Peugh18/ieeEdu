<?php

namespace App\Mail;

use App\Models\ConsultancyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientConsultancyAutoReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $consultancyRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(ConsultancyRequest $consultancyRequest)
    {
        $this->consultancyRequest = $consultancyRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recibimos tu solicitud de Consultoría - Instituto de Economía y Empresa',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.consultancy.client_autoreply',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
