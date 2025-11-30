<?php

namespace App\Models\Eventos;

use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\Voluntario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EscalaAuto extends Model
{
    use HasFactory;

    protected $table = 'escala_auto';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'voluntario_id',
        'data',
        'funcao',
        'status',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function voluntario(): BelongsTo
    {
        return $this->belongsTo(Voluntario::class, 'voluntario_id');
    }
}
