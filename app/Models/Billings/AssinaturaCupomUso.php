<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaCupomUso extends Model
{
    protected $table = 'assinatura_cupons_uso';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'assinatura_id',
        'igreja_id',
        'pacote_id',
        'cupom_id',
        'usado_em',
    ];

    protected $casts = [
        'usado_em' => 'datetime',
    ];

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(AssinaturaHistorico::class, 'assinatura_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_id');
    }

    public function cupom(): BelongsTo
    {
        return $this->belongsTo(AssinaturaCupom::class, 'cupom_id');
    }
}

