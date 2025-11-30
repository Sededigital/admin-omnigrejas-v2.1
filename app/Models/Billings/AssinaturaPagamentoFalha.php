<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\Billings\AssinaturaLog;
use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaPagamento;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaPagamentoFalha extends Model
{
    protected $table = 'assinatura_pagamentos_falhas';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'pagamento_id',
        'igreja_id',
        'motivo',
        'data',
        'resolvido',
    ];

    protected $casts = [
        'data'      => 'datetime',
        'resolvido' => 'boolean',
    ];

    public function pagamento(): BelongsTo
    {
        return $this->belongsTo(AssinaturaPagamento::class, 'pagamento_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssinaturaLog::class, 'pagamento_id', 'pagamento_id');
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function isResolvido(): bool
    {
        return $this->resolvido;
    }

    public function isPendente(): bool
    {
        return !$this->resolvido;
    }

    public function marcarComoResolvido(): void
    {
        $this->update(['resolvido' => true]);
    }

    public function marcarComoPendente(): void
    {
        $this->update(['resolvido' => false]);
    }

    public function getDataFormatada(): string
    {
        return $this->data->format('d/m/Y H:i');
    }

    public function getDataRelativa(): string
    {
        return $this->data->diffForHumans();
    }

    public function getMotivoFormatado(): string
    {
        return ucfirst($this->motivo);
    }

    public function getStatusFormatado(): string
    {
        return $this->isResolvido() ? 'Resolvido' : 'Pendente';
    }

    public function getStatusClass(): string
    {
        return $this->isResolvido() ? 'success' : 'danger';
    }

    public function getDiasDesdeFalha(): int
    {
        return $this->data->diffInDays(now());
    }

    public function isFalhaRecente(int $dias = 7): bool
    {
        return $this->getDiasDesdeFalha() <= $dias;
    }

    public function isFalhaAntiga(int $dias = 30): bool
    {
        return $this->getDiasDesdeFalha() > $dias;
    }
}
