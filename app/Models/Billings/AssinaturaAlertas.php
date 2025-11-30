<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_alerta', $tipo);
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

    // 🔗 HELPERS
    public function isExpirado(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isAtivo(): bool
    {
        return !$this->isExpirado();
    }

    public function marcarComoLido(): void
    {
        $this->update([
            'lido' => true,
            'lido_em' => now()
        ]);
    }

    public function getTipoFormatado(): string
    {
        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'Expiração Próxima',
            'limite_proximo' => 'Limite Próximo',
            'expirado' => 'Expirado',
            'limite_excedido' => 'Limite Excedido',
            default => ucfirst(str_replace('_', ' ', $this->tipo_alerta))
        };
    }

    public function getStatusFormatado(): string
    {
        if ($this->lido) {
            return 'Lido';
        }

        if ($this->isExpirado()) {
            return 'Expirado';
        }

        return 'Pendente';
    }

    public function getBadgeClass(): string
    {
        if ($this->lido) {
            return 'secondary';
        }

        return match($this->tipo_alerta) {
            'expiracao_proxima' => 'warning',
            'limite_proximo' => 'info',
            'expirado' => 'danger',
            'limite_excedido' => 'danger',
            default => 'primary'
        };
    }

    public function getDadosFormatados(): array
    {
        return $this->dados ?? [];
    }

    public function getCriadoEmFormatado(): string
    {
        return $this->criado_em->format('d/m/Y H:i');
    }

    public function getLidoEmFormatado(): ?string
    {
        return $this->lido_em ? $this->lido_em->format('d/m/Y H:i') : null;
    }

    public function getExpiresAtFormatado(): ?string
    {
        return $this->expires_at ? $this->expires_at->format('d/m/Y H:i') : null;
    }
}
