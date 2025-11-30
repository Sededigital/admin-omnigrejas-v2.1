<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaCiclo extends Model
{
    protected $table = 'assinatura_ciclos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'assinatura_id',
        'inicio',
        'fim',
        'valor',
        'status',
        'gerado_em',
    ];

    protected $casts = [
        'inicio'    => 'date',
        'fim'       => 'date',
        'valor'     => 'decimal:2',
        'gerado_em' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(AssinaturaHistorico::class, 'assinatura_id');
    }

    // 🔗 RELACIONAMENTOS ATRAVÉS DA ASSINATURA
    public function igreja()
    {
        return $this->hasOneThrough(
            Igreja::class,
            AssinaturaHistorico::class,
            'id', // Foreign key on assinatura_historico table
            'id', // Foreign key on igrejas table
            'assinatura_id', // Local key on assinatura_ciclos table
            'igreja_id' // Local key on assinatura_historico table
        );
    }

    public function pacote()
    {
        return $this->hasOneThrough(
            Pacote::class,
            AssinaturaHistorico::class,
            'id', // Foreign key on assinatura_historico table
            'id', // Foreign key on pacotes table
            'assinatura_id', // Local key on assinatura_ciclos table
            'pacote_id' // Local key on assinatura_historico table
        );
    }
}
