<?php

namespace App\Models\Igrejas;

use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\AssinaturaAutoRenovacao;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IgrejaMetodoPagamento extends Model
{
    protected $table = 'igrejas_metodos_pagamento';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'igreja_id',
        'tipo',
        'detalhes',
        'ativo',
        'criado_em',
    ];

    protected $casts = [
        'detalhes'  => 'array',
        'ativo'     => 'boolean',
        'criado_em' => 'datetime',
    ];

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function autoRenovacoes(): HasMany
    {
        return $this->hasMany(AssinaturaAutoRenovacao::class, 'metodo_pagamento_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(AssinaturaPagamento::class, 'metodo_pagamento', 'tipo');
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function isInativo(): bool
    {
        return !$this->ativo;
    }

    public function ativar(): void
    {
        $this->update(['ativo' => true]);
    }

    public function desativar(): void
    {
        $this->update(['ativo' => false]);
    }

    public function getTipoFormatado(): string
    {
        return match($this->tipo) {
            'cash' => 'Dinheiro',
            'multicaixa_express' => 'Multicaixa Express',
            'tpa' => 'TPA',
            'transferencia' => 'Transferência',
            'deposito' => 'Depósito',
            default => ucfirst($this->tipo)
        };
    }

    public function getTipoClass(): string
    {
        return match($this->tipo) {
            'cash' => 'success',
            'multicaixa_express' => 'primary',
            'tpa' => 'info',
            'transferencia' => 'warning',
            'deposito' => 'secondary',
            default => 'secondary'
        };
    }

    public function getDataCriacaoFormatada(): string
    {
        return $this->criado_em->format('d/m/Y H:i');
    }

    public function getDataCriacaoRelativa(): string
    {
        return $this->criado_em->diffForHumans();
    }

    public function getDetalhesFormatados(): array
    {
        if (is_string($this->detalhes)) {
            return json_decode($this->detalhes, true) ?? [];
        }

        return $this->detalhes ?? [];
    }

    public function getValorDetalhes($chave, $padrao = null)
    {
        $detalhes = $this->getDetalhesFormatados();
        return $detalhes[$chave] ?? $padrao;
    }

    public function isMulticaixaExpress(): bool
    {
        return $this->tipo === 'multicaixa_express';
    }

    public function isTPA(): bool
    {
        return $this->tipo === 'tpa';
    }

    public function isTransferencia(): bool
    {
        return $this->tipo === 'transferencia';
    }

    public function isDeposito(): bool
    {
        return $this->tipo === 'deposito';
    }

    public function isCash(): bool
    {
        return $this->tipo === 'cash';
    }

    public function getIcone(): string
    {
        return match($this->tipo) {
            'cash' => 'fas fa-money-bill-wave',
            'multicaixa_express' => 'fas fa-credit-card',
            'tpa' => 'fas fa-mobile-alt',
            'transferencia' => 'fas fa-exchange-alt',
            'deposito' => 'fas fa-university',
            default => 'fas fa-credit-card'
        };
    }
}
