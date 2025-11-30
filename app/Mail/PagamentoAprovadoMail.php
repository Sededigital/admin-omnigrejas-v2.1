<?php

namespace App\Mail;

use App\Models\Billings\PagamentoAssinaturaIgreja;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PagamentoAprovadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pagamento;
    public $datas;

    /**
     * Create a new message instance.
     */
    public function __construct(PagamentoAssinaturaIgreja $pagamento, array $datas)
    {
        $this->pagamento = $pagamento;
        $this->datas = $datas;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Pagamento Aprovado - Assinatura Ativada!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pagamento-aprovado',
            with: [
                'pagamento' => $this->pagamento,
                'datas' => $this->datas,
                'pacoteNome' => $this->pagamento->pacote_nome,
                'valor' => $this->pagamento->getValorFormatado(),
                'dataInicio' => $this->datas['data_inicio']->format('d/m/Y'),
                'dataFim' => $this->datas['data_fim'] ? $this->datas['data_fim']->format('d/m/Y') : 'Vitalício',
                'tipoAssinatura' => $this->datas['vitalicio'] ? 'Vitalício' : $this->datas['duracao_meses'] . ' meses',
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