<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceiroConta extends Model
{
    use HasFactory;

    protected $table = 'financeiro_contas';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'banco',
        'titular',
        'iban',
        'swift',
        'numero_conta',
        'moeda',
        'ativa',
        'observacoes',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'moeda' => 'string',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Igrejas\Igreja::class, 'igreja_id');
    }

    public function movimentos(): HasMany
    {
        return $this->hasMany(FinanceiroMovimento::class, 'conta_id');
    }
}
