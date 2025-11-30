<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;
    public $igrejaNome;

    public function __construct(User $user, string $plainPassword, string $igrejaNome)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->igrejaNome = $igrejaNome;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Suas Credenciais de Acesso - ' . $this->igrejaNome,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.member-credentials',
            with: [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'igrejaNome' => $this->igrejaNome,
                'loginUrl' => url('/login'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
