<?php

namespace App\Models\Billings\Trial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrialAlerta extends Model
{
    use HasFactory;

    protected $table = 'trial_alertas';

    protected $fillable = [
        'trial_user_id',
        'tipo_alerta',
        'titulo',
        'mensagem',
        'dados',
        'enviado_em',
        'lido_em',
        'email_enviado',
    ];

    protected $casts = [
        'dados' => 'array',
        'enviado_em' => 'datetime',
        'lido_em' => 'datetime',
        'email_enviado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function trialUser(): BelongsTo
    {
        return $this->belongsTo(TrialUser::class, 'trial_user_id');
    }

    // 🔗 SCOPES
    public function scopeNaoEnviados($query)
    {
        return $query->whereNull('enviado_em');
    }

    public function scopeEnviados($query)
    {
        return $query->whereNotNull('enviado_em');
    }

    public function scopeNaoLidos($query)
    {
        return $query->whereNull('lido_em');
    }

    public function scopeLidos($query)
    {
        return $query->whereNotNull('lido_em');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_alerta', $tipo);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeComEmailEnviado($query)
    {
        return $query->where('email_enviado', true);
    }

    public function scopeSemEmailEnviado($query)
    {
        return $query->where('email_enviado', false);
    }

    // 🔗 HELPERS
    public function foiEnviado(): bool
    {
        return !is_null($this->enviado_em);
    }

    public function foiLido(): bool
    {
        return !is_null($this->lido_em);
    }

    public function emailFoiEnviado(): bool
    {
        return $this->email_enviado;
    }

    public function getTipoFormatado(): string
    {
        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'Expiração Próxima',
            'expirado' => 'Expirado',
            'bloqueado' => 'Bloqueado',
            'reativado' => 'Reativado',
            'cancelado' => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', $this->tipo_alerta))
        };
    }

    public function getTipoBadgeClass(): string
    {
        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'warning',
            'expirado' => 'danger',
            'bloqueado' => 'dark',
            'reativado' => 'success',
            'cancelado' => 'secondary',
            default => 'secondary'
        };
    }

    public function getTipoIcone(): string
    {
        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'fas fa-clock',
            'expirado' => 'fas fa-times-circle',
            'bloqueado' => 'fas fa-ban',
            'reativado' => 'fas fa-check-circle',
            'cancelado' => 'fas fa-times',
            default => 'fas fa-bell'
        };
    }

    public function getEnviadoEmFormatado(): string
    {
        return $this->enviado_em ? $this->enviado_em->format('d/m/Y H:i') : 'Não enviado';
    }

    public function getLidoEmFormatado(): string
    {
        return $this->lido_em ? $this->lido_em->format('d/m/Y H:i') : 'Não lido';
    }

    public function getCriadoEmFormatado(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getDadosFormatados(): array
    {
        return $this->dados ?? [];
    }

    public function getValorDados($chave, $padrao = null)
    {
        $dados = $this->getDadosFormatados();
        return $dados[$chave] ?? $padrao;
    }

    public function marcarComoEnviado(): void
    {
        $this->update([
            'enviado_em' => now(),
            'email_enviado' => true,
        ]);
    }

    public function marcarComoLido(): void
    {
        $this->update(['lido_em' => now()]);
    }

    public function atualizarMensagem($novaMensagem): void
    {
        $this->update(['mensagem' => $novaMensagem]);
    }

    public function atualizarDados($novosDados): void
    {
        $dadosAtuais = $this->getDadosFormatados();
        $dadosAtualizados = array_merge($dadosAtuais, $novosDados);

        $this->update(['dados' => $dadosAtualizados]);
    }

    public function deveSerReenviado(): bool
    {
        // Reenviar se não foi enviado ou foi enviado há mais de 24 horas
        return !$this->emailFoiEnviado() ||
               ($this->enviado_em && $this->enviado_em->diffInHours(now()) > 24);
    }

    public function getDiasDesdeCriacao(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getDiasDesdeEnvio(): ?int
    {
        return $this->enviado_em ? $this->enviado_em->diffInDays(now()) : null;
    }

    public function getDiasDesdeLeitura(): ?int
    {
        return $this->lido_em ? $this->lido_em->diffInDays(now()) : null;
    }
}