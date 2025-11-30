<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\Billings\Pacote;
use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaHistorico;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaUpgrade extends Model
{
    protected $table = 'assinatura_upgrades';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'assinatura_id',
        'igreja_id',
        'pacote_anterior',
        'pacote_novo',
        'valor_diferenca',
        'data_upgrade',
        'motivo',
        'usuario_id',
    ];

    protected $casts = [
        'valor_diferenca' => 'decimal:2',
        'data_upgrade'    => 'date',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(AssinaturaHistorico::class, 'assinatura_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function pacoteAnterior(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_anterior');
    }

    public function pacoteNovo(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_novo');
    }
}

