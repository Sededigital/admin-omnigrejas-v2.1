<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaConsumo extends Model
{
    use HasFactory;

    protected $table = 'igreja_consumo';

    protected $fillable = [
        'igreja_id',
        'recurso_tipo',
        'consumo_atual',
        'limite_atual',
        'periodo_referencia',
        'reset_automatico',
        'ultimo_reset',
    ];

    protected $casts = [
        'consumo_atual' => 'integer',
        'limite_atual' => 'integer',
        'periodo_referencia' => 'date',
        'reset_automatico' => 'boolean',
        'ultimo_reset' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    // 🔗 SCOPES
    public function scopeDoMesAtual($query)
    {
        return $query->where('periodo_referencia', now()->startOfMonth());
    }

    public function scopePorRecurso($query, $recursoTipo)
    {
        return $query->where('recurso_tipo', $recursoTipo);
    }

    public function scopeComLimite($query)
    {
        return $query->whereNotNull('limite_atual');
    }

    public function scopeSemLimite($query)
    {
        return $query->whereNull('limite_atual');
    }

    public function scopeAcimaDoLimite($query)
    {
        return $query->whereRaw('consumo_atual > limite_atual')
                    ->whereNotNull('limite_atual');
    }

    public function scopeProximoDoLimite($query, $percentual = 80)
    {
        return $query->whereRaw('(consumo_atual::decimal / limite_atual) * 100 >= ?', [$percentual])
                    ->whereNotNull('limite_atual');
    }

    // 🔗 HELPERS
    public function isIlimitado(): bool
    {
        return is_null($this->limite_atual);
    }

    public function isLimitado(): bool
    {
        return !is_null($this->limite_atual);
    }

    public function atingiuLimite(): bool
    {
        return $this->isLimitado() && $this->consumo_atual >= $this->limite_atual;
    }

    public function estaProximoDoLimite($percentual = 80): bool
    {
        if (!$this->isLimitado()) {
            return false;
        }

        $percentualAtual = ($this->consumo_atual / $this->limite_atual) * 100;
        return $percentualAtual >= $percentual;
    }

    public function getPercentualUso(): float
    {
        if (!$this->isLimitado()) {
            return 0;
        }

        return round(($this->consumo_atual / $this->limite_atual) * 100, 2);
    }

    public function getDisponivel(): ?int
    {
        if (!$this->isLimitado()) {
            return null; // Ilimitado
        }

        return max(0, $this->limite_atual - $this->consumo_atual);
    }

    public function incrementarConsumo($quantidade = 1): bool
    {
        if ($this->isLimitado() && ($this->consumo_atual + $quantidade) > $this->limite_atual) {
            return false; // Não pode exceder limite
        }

        $this->increment('consumo_atual', $quantidade);
        return true;
    }

    public function decrementarConsumo($quantidade = 1): bool
    {
        if ($this->consumo_atual - $quantidade < 0) {
            return false; // Não pode ficar negativo
        }

        $this->decrement('consumo_atual', $quantidade);
        return true;
    }

    public function resetConsumo(): void
    {
        $this->update([
            'consumo_atual' => 0,
            'ultimo_reset' => now()
        ]);
    }

    public function atualizarLimite($novoLimite): void
    {
        $this->update(['limite_atual' => $novoLimite]);
    }

    public function getRecursoFormatado(): string
    {
        try {
            $permissao = \App\Models\RBAC\IgrejaPermissao::where('codigo', $this->recurso_tipo)->first();
            return $permissao ? $permissao->nome : ucfirst($this->recurso_tipo);
        } catch (\Exception $e) {
            return ucfirst($this->recurso_tipo);
        }
    }

    public function getIcone(): string
    {
        return 'fas fa-cogs'; // Ícone genérico para permissões
    }

    public function getStatusBadgeClass(): string
    {
        if ($this->atingiuLimite()) {
            return 'danger';
        }

        if ($this->estaProximoDoLimite(80)) {
            return 'warning';
        }

        return 'success';
    }

    public function getStatusText(): string
    {
        if ($this->atingiuLimite()) {
            return 'Limite Atingido';
        }

        if ($this->estaProximoDoLimite(80)) {
            return 'Próximo do Limite';
        }

        return 'Dentro do Limite';
    }
}
