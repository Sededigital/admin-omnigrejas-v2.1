<?php

namespace App\Mail;

use App\Models\Igrejas\Igreja;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccessCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $igreja;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Igreja $igreja, User $user)
    {
        $this->igreja = $igreja;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de Acesso - ' . $this->igreja->nome,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.access-code',
            with: [
                'igreja' => $this->igreja,
                'user' => $this->user,
                'code' => $this->igreja->code_access,
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
