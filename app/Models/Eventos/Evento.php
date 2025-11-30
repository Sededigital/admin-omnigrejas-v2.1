<?php

namespace App\Models\Eventos;

use App\Models\User;
use App\Models\Eventos\Escala;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eventos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'titulo',
        'tipo',
        'data_evento',
        'hora_inicio',
        'hora_fim',
        'local_evento',
        'descricao',
        'created_by',
        'responsavel',
        'status',
    ];

    protected $casts = [
        'data_evento' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim'    => 'datetime:H:i',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function responsavelUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel');
    }

    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class, 'culto_evento_id');
    }

    public function relatoriosCulto(): HasMany
    {
        return $this->hasMany(\App\Models\Igrejas\RelatorioCulto::class, 'evento_id');
    }
}
