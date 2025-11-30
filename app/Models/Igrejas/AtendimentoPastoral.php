<?php

namespace App\Models\Igrejas;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtendimentoPastoral extends Model
{
    use HasFactory;

    protected $table = 'atendimentos_pastorais';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'membro_id',
        'pastor_id',
        'tipo',
        'descricao',
        'data_atendimento',
    ];

    protected $casts = [
        'data_atendimento' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    public function pastor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pastor_id');
    }
}
