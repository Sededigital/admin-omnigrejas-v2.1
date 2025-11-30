<?php

namespace App\Mail;

use App\Models\Billings\Trial\TrialUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpirandoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $trial;
    public $diasRestantes;

    /**
     * Create a new message instance.
     */
    public function __construct(TrialUser $trial, int $diasRestantes)
    {
        $this->trial = $trial;
        $this->diasRestantes = $diasRestantes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $titulo = $this->diasRestantes === 1
            ? 'Seu período de teste expira amanhã'
            : "Seu período de teste expira em {$this->diasRestantes} dias";

        return new Envelope(
            subject: $titulo . ' - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-expirando',
            with: [
                'trial' => $this->trial,
                'diasRestantes' => $this->diasRestantes,
                'dataExpiracao' => $this->trial->data_fim->format('d/m/Y'),
                'nomeUsuario' => $this->trial->user->name,
                'igrejaNome' => $this->trial->igreja->nome,
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