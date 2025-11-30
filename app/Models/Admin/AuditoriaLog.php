<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditoriaLog extends Model
{
    use HasFactory;

    protected $table = 'auditoria_logs';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tabela',
        'registro_id',
        'acao',
        'usuario_id',
        'data_acao',
        'valores',
    ];

    protected $casts = [
        'data_acao' => 'datetime',
        'valores'   => 'array',
    ];

    // 🔗 RELACIONAMENTOS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
