<?php

namespace App\Models\Igrejas;

use Illuminate\Database\Eloquent\Model;

class HabilidadesMembro extends Model
{
    protected $table = 'habilidades_membros';
    public $incrementing = false;
    protected $primaryKey = null;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'membro_id',
        'habilidade',
        'nivel',
    ];

    // 🔗 RELACIONAMENTOS
    public function membro()
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }
}
