<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RecoveryCodeSent extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $recoveryCode;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $recoveryCode)
    {
        $this->user = $user;
        $this->recoveryCode = $recoveryCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de Recuperação 2FA',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recovery-code-sent',
            with: [
                'user' => $this->user,
                'recoveryCode' => $this->recoveryCode,
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
