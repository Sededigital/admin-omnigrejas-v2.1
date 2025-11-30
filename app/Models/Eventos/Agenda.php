<?php

namespace App\Models\Eventos;

use App\Models\User;
use App\Models\Eventos\Evento;
use App\Models\Eventos\Agendamento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false; // só tem created_at

    protected $fillable = [
        'user_id',
        'evento_id',
        'agendamento_id',
        'lembrete',
        'status',
        'tipo_lembrete',
        'titulo_personalizado',
        'mensagem_personalizada',
        'created_at',
    ];

    protected $casts = [
        'lembrete' => 'string', // INTERVAL precisa ser tratado como string no PHP
        'created_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }
}
