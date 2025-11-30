<?php

namespace App\Models\Igrejas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voluntario extends Model
{
    use HasFactory;

    protected $table = 'voluntarios';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'membro_id',
        'area_interesse',
        'disponibilidade',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // 🔗 RELACIONAMENTOS
    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    public function escalas(): HasMany
    {
        return $this->hasMany(EscalaAuto::class, 'voluntario_id');
    }
}
