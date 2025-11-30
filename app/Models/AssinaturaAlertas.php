<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Igrejas\Igreja;

class AssinaturaAlertas extends Model
{
    use HasFactory;

    protected $table = 'assinatura_alertas';

    protected $fillable = [
        'igreja_id',
        'tipo_alerta',
        'titulo',
        'mensagem',
        'dados',
        'lido',
        'lido_em',
        'criado_em',
        'expires_at',
    ];

    protected $casts = [
        'dados' => 'array',
        'lido' => 'boolean',
        'lido_em' => 'datetime',
        'criado_em' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    // 🔗 SCOPES
    public function scopeNaoLidos($query)
    {
        return $query->where('lido', false);
    }

    public function scopeLidos($query)
    {
        return $query->where('lido', true);
    }

    public function scopeAtivos($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpirados($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_alerta', $tipo);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('criado_em', '>=', now()->subDays($dias));
    }

    public function scopeAntigos($query, $dias = 30)
    {
        return $query->where('criado_em', '<', now()->subDays($dias));
    }

    // 🔗 HELPERS
    public function isLido(): bool
    {
        return $this->lido;
    }

    public function isNaoLido(): bool
    {
        return !$this->lido;
    }

    public function estaAtivo(): bool
    {
        return is_null($this->expires_at) || $this->expires_at->isFuture();
    }

    public function estaExpirado(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function foiLido(): bool
    {
        return !is_null($this->lido_em);
    }

    public function getTipoFormatado(): string
    {
        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'Expiração Próxima',
            'limite_proximo' => 'Limite Próximo',
            'expirado' => 'Expirado',
            default => ucfirst(str_replace('_', ' ', $this->tipo_alerta))
        };
    }

    public function getTipoClass(): string
    {
        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'warning',
            'limite_proximo' => 'info',
            'expirado' => 'danger',
            default => 'secondary'
        };
    }

    public function getTipoIcone(): string
    {
        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'fas fa-clock',
            'limite_proximo' => 'fas fa-exclamation-triangle',
            'expirado' => 'fas fa-times-circle',
            default => 'fas fa-bell'
        };
    }

    public function getCriadoEmFormatado(): string
    {
        return $this->criado_em->format('d/m/Y H:i');
    }

    public function getCriadoEmRelativo(): string
    {
        return $this->criado_em->diffForHumans();
    }

    public function getLidoEmFormatado(): string
    {
        return $this->lido_em ? $this->lido_em->format('d/m/Y H:i') : 'Não lido';
    }

    public function getLidoEmRelativo(): string
    {
        return $this->lido_em ? $this->lido_em->diffForHumans() : 'Não lido';
    }

    public function getExpiresAtFormatado(): string
    {
        return $this->expires_at ? $this->expires_at->format('d/m/Y H:i') : 'Nunca';
    }

    public function getExpiresAtRelativo(): string
    {
        return $this->expires_at ? $this->expires_at->diffForHumans() : 'Nunca';
    }

    public function getDadosFormatados(): array
    {
        if (is_string($this->dados)) {
            return json_decode($this->dados, true) ?? [];
        }

        return $this->dados ?? [];
    }

    public function getValorDados($chave, $padrao = null)
    {
        $dados = $this->getDadosFormatados();
        return $dados[$chave] ?? $padrao;
    }

    public function marcarComoLido(): void
    {
        $this->update([
            'lido' => true,
            'lido_em' => now()
        ]);
    }

    public function marcarComoNaoLido(): void
    {
        $this->update([
            'lido' => false,
            'lido_em' => null
        ]);
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

    public function definirExpiracao($dias): void
    {
        $this->update(['expires_at' => now()->addDays($dias)]);
    }

    public function removerExpiracao(): void
    {
        $this->update(['expires_at' => null]);
    }

    public function getDiasDesdeCriacao(): int
    {
        return $this->criado_em->diffInDays(now());
    }

    public function getDiasParaExpiracao(): ?int
    {
        if (!$this->expires_at) return null;
        return now()->diffInDays($this->expires_at, false);
    }

    public function deveSerRemovido(): bool
    {
        return $this->estaExpirado() && $this->isLido();
    }

    public function getPrioridade(): int
    {
        return match($this->tipo_alerta) {
            'expirado' => 1,
            'expiracao_proxima' => 2,
            'limite_proximo' => 3,
            default => 4
        };
    }
}
