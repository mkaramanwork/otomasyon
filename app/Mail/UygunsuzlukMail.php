<?php

namespace App\Mail;

use App\Models\Uygunsuzluk;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UygunsuzlukMail extends Mailable
{
    use Queueable, SerializesModels;

    public $uygunsuzluk;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(Uygunsuzluk $uygunsuzluk, string $type)
    {
        $this->uygunsuzluk = $uygunsuzluk;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uygunsuzluk Bildirimi',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'uygunsuzluk.mail', // resources/views/uygunsuzluk/mail.blade.php
            with: [
                'uygunsuzluk' => $this->uygunsuzluk,
                'type' => $this->type,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}