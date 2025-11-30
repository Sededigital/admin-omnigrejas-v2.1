<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use App\Models\Igrejas\IgrejaMetodoPagamento;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaAutoRenovacao extends Model
{
    protected $table = 'assinatura_auto_renovacao';
    protected $primaryKey = 'igreja_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'igreja_id',
        'ativo',
        'metodo_pagamento_id',
        'ultima_tentativa',
        'proxima_tentativa',
    ];

    protected $casts = [
        'ativo'            => 'boolean',
        'ultima_tentativa' => 'datetime',
        'proxima_tentativa'=> 'datetime',
    ];

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function metodoPagamento(): BelongsTo
    {
        return $this->belongsTo(IgrejaMetodoPagamento::class, 'metodo_pagamento_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssinaturaLog::class, 'igreja_id', 'igreja_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(AssinaturaPagamento::class, 'igreja_id', 'igreja_id');
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

    public function temMetodoPagamento(): bool
    {
        return !is_null($this->metodo_pagamento_id);
    }

    public function getUltimaTentativaFormatada(): string
    {
        return $this->ultima_tentativa ? $this->ultima_tentativa->format('d/m/Y H:i') : 'Nunca';
    }

    public function getProximaTentativaFormatada(): string
    {
        return $this->proxima_tentativa ? $this->proxima_tentativa->format('d/m/Y H:i') : 'N/A';
    }

    public function getUltimaTentativaRelativa(): string
    {
        return $this->ultima_tentativa ? $this->ultima_tentativa->diffForHumans() : 'Nunca';
    }

    public function getProximaTentativaRelativa(): string
    {
        return $this->proxima_tentativa ? $this->proxima_tentativa->diffForHumans() : 'N/A';
    }

    public function isTentativaPendente(): bool
    {
        return $this->proxima_tentativa && $this->proxima_tentativa->isPast();
    }

    public function isTentativaFutura(): bool
    {
        return $this->proxima_tentativa && $this->proxima_tentativa->isFuture();
    }

    public function getDiasProximaTentativa(): int
    {
        if (!$this->proxima_tentativa) {
            return 0;
        }

        return $this->proxima_tentativa->diffInDays(now());
    }

    public function agendarProximaTentativa(int $dias = 1): void
    {
        $this->update([
            'proxima_tentativa' => now()->addDays($dias)
        ]);
    }

    public function registrarTentativa(): void
    {
        $this->update([
            'ultima_tentativa' => now()
        ]);
    }

    public function podeTentarRenovacao(): bool
    {
        return $this->isAtivo() &&
               $this->temMetodoPagamento() &&
               $this->isTentativaPendente();
    }
}
