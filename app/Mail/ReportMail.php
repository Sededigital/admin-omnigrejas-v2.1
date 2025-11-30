<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Igrejas\RelatorioCulto;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $report;
    public $pdfPath;
    public $igreja;

    /**
     * Create a new message instance.
     */
    public function __construct(RelatorioCulto $report, string $pdfPath)
    {
        $this->report = $report;
        $this->pdfPath = $pdfPath;
        $this->igreja = $report->igreja;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Relatório de Culto - ' . $this->igreja->nome,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath($this->pdfPath)
                ->as('relatorio-culto-' . ($this->report->titulo ?: 'sem-titulo') . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
