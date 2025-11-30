<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $alertType;
    public $alertData;

    public function __construct(User $user, string $alertType, array $alertData = [])
    {
        $this->user = $user;
        $this->alertType = $alertType;
        $this->alertData = $alertData;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'new_login' => '🔐 Novo Login Detectado',
            'suspicious_activity' => '🚨 Atividade Suspeita Detectada',
            'password_changed' => '🔑 Senha Alterada',
            '2fa_disabled' => '⚠️ 2FA Desativado',
            'unusual_location' => '🌍 Login de Localização Incomum',
        ];

        return new Envelope(
            subject: $subjects[$this->alertType] ?? 'Alerta de Segurança',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.security-alert',
            with: [
                'user' => $this->user,
                'alertType' => $this->alertType,
                'alertData' => $this->alertData,
            ],
        );
    }
}
