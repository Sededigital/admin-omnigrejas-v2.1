<?php

namespace App\Models\Eventos;

use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Escala extends Model
{
    use HasFactory;

    protected $table = 'escalas';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'culto_evento_id',
        'membro_id',
        'funcao',
        'observacoes',
    ];

    // 🔗 RELACIONAMENTOS
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'culto_evento_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }
}
