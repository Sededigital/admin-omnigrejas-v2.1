<?php

namespace App\Mail;

use App\Models\Billings\Trial\TrialUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialCriadoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $trial;
    public $senhaTemporaria;

    /**
     * Create a new message instance.
     */
    public function __construct(TrialUser $trial, string $senhaTemporaria)
    {
        $this->trial = $trial;
        $this->senhaTemporaria = $senhaTemporaria;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao ' . config('app.name') . ' - Seu período de teste começou!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-criado',
            with: [
                'trial' => $this->trial,
                'senhaTemporaria' => $this->senhaTemporaria,
                'dataInicio' => $this->trial->data_inicio->format('d/m/Y'),
                'dataFim' => $this->trial->data_fim->format('d/m/Y'),
                'nomeUsuario' => $this->trial->user->name,
                'emailUsuario' => $this->trial->user->email,
                'igrejaNome' => $this->trial->igreja->nome,
                'periodoDias' => $this->trial->periodo_dias,
                'loginUrl' => url('/login'),
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