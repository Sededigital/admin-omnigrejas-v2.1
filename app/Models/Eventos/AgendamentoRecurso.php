<?php

namespace App\Models\Eventos;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Outros\Recurso;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendamentoRecurso extends Model
{
    use HasFactory;

    protected $table = 'agendamentos_recursos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'recurso_id',
        'igreja_id',
        'inicio',
        'fim',
        'reservado_por',
        'status',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function recurso(): BelongsTo
    {
        return $this->belongsTo(Recurso::class, 'recurso_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function usuarioReservou(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reservado_por');
    }
}
