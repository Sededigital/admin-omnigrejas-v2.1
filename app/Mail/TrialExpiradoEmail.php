<?php

namespace App\Mail;

use App\Models\Billings\Trial\TrialUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiradoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $trial;

    /**
     * Create a new message instance.
     */
    public function __construct(TrialUser $trial)
    {
        $this->trial = $trial;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu período de teste expirou - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-expirado',
            with: [
                'trial' => $this->trial,
                'dataExpiracao' => $this->trial->data_fim->format('d/m/Y'),
                'nomeUsuario' => $this->trial->user->name,
                'igrejaNome' => $this->trial->igreja->nome,
                'diasAtivos' => $this->trial->diasDesdeCriacao(),
                'podeReativar' => $this->trial->podeSerReativado(),
                'totalMembros' => $this->trial->total_membros_criados,
                'totalPosts' => $this->trial->total_posts_criados,
                'totalEventos' => $this->trial->total_eventos_criados,
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