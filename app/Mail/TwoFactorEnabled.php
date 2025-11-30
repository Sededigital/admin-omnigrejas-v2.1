<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TwoFactorEnabled extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $recoveryCodes;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, array $recoveryCodes = [])
    {
        $this->user = $user;
        $this->recoveryCodes = $recoveryCodes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Autenticação de Dois Fatores Ativada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-enabled',
            with: [
                'user' => $this->user,
                'recoveryCodes' => $this->recoveryCodes,
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
