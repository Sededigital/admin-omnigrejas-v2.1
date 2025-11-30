<?php

namespace App\Models\Chats;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaChat extends Model
{
    use HasFactory;

    protected $table = 'igreja_chats';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'igreja_id',
        'nome',
        'descricao',
        'criado_por',
        'visibilidade',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(IgrejaChatMensagem::class, 'chat_id');
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(IgrejaChatParticipante::class, 'chat_id');
    }
}
