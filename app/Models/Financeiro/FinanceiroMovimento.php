<?php

namespace App\Models\Financeiro;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinanceiroMovimento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'financeiro_movimentos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'conta_id',
        'tipo',
        'categoria_id',
        'valor',
        'descricao',
        'data_transacao',
        'metodo_pagamento',
        'responsavel_id',
        'comprovante_url',
        'created_by',
    ];

    protected $casts = [
        'data_transacao' => 'date',
        'valor'          => 'decimal:2',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(FinanceiroCategoria::class, 'categoria_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function conta(): BelongsTo
    {
        return $this->belongsTo(FinanceiroConta::class, 'conta_id');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(FinanceiroAuditoria::class, 'movimento_id');
    }

    // 🔗 HELPERS
    public function isEntrada(): bool
    {
        return $this->tipo === 'entrada';
    }

    public function isSaida(): bool
    {
        return $this->tipo === 'saida';
    }

    public function getTipoFormatado(): string
    {
        return match($this->tipo) {
            'entrada' => 'Entrada',
            'saida' => 'Saída',
            default => 'Desconhecido'
        };
    }

    public function getValorFormatado(): string
    {
        return number_format($this->valor, 2, ',', '.') . ' AOA';
    }

    public function getBadgeClass(): string
    {
        return match($this->tipo) {
            'entrada' => 'success',
            'saida' => 'danger',
            default => 'secondary'
        };
    }

    // 🔗 SCOPES
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    public function scopeSaidas($query)
    {
        return $query->where('tipo', 'saida');
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_transacao', [$dataInicio, $dataFim]);
    }

    public function scopePorConta($query, $contaId)
    {
        return $query->where('conta_id', $contaId);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }
}
