<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\Billings\Pacote;
use App\Models\Billings\AssinaturaLog;
use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaCiclo;
use App\Models\Billings\AssinaturaUpgrade;
use App\Models\Billings\AssinaturaCupomUso;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\AssinaturaNotificacao;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssinaturaHistorico extends Model
{
    use HasFactory;

    protected $table = 'assinatura_historico';
    protected $primaryKey = 'id';

    protected $fillable = [
        'igreja_id',
        'pacote_id',
        'data_inicio',
        'data_fim',
        'valor',
        'status',
        'forma_pagamento',
        'transacao_id',
        'trial_fim',
        'duracao_meses_custom',
        'vitalicio',
    ];

    protected $casts = [
        'data_inicio'          => 'date',
        'data_fim'             => 'date',
        'trial_fim'            => 'date',
        'valor'                => 'decimal:2',
        'duracao_meses_custom' => 'integer',
        'vitalicio'            => 'boolean',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(AssinaturaPagamento::class, 'assinatura_id');
    }

    public function ciclos(): HasMany
    {
        return $this->hasMany(AssinaturaCiclo::class, 'assinatura_id');
    }

    public function cuponsUsados(): HasMany
    {
        return $this->hasMany(AssinaturaCupomUso::class, 'assinatura_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssinaturaLog::class, 'assinatura_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(AssinaturaNotificacao::class, 'assinatura_id');
    }

    public function upgrades(): HasMany
    {
        return $this->hasMany(AssinaturaUpgrade::class, 'assinatura_id');
    }

    // Helpers
    public function getDuracaoMeses(): int
    {
        if ($this->vitalicio) {
            return 9999; // vitalício => duração infinita simbólica
        }

        return $this->data_inicio->diffInMonths($this->data_fim);
    }

    public function getValorMensal(): float
    {
        $duracao = $this->getDuracaoMeses();
        return $duracao > 0 ? $this->valor / $duracao : $this->valor;
    }

    public function getValorFormatado(): string
    {
        return number_format($this->valor, 2, ',', '.') . ' AOA';
    }
}
