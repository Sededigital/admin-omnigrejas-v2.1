<?php

namespace App\Models\Igrejas;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class IgrejaMembrosMinisterio extends Pivot
{
    use SoftDeletes;

    protected $table = 'igreja_membros_ministerios';
    public $incrementing = false; // chave primária composta
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'membro_id',
        'ministerio_id',
        'funcao'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
