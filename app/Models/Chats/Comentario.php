<?php

namespace App\Models\Chats;

use App\Models\User;
use App\Models\Eventos\Evento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentarios';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'post_id',
        'evento_id',
        'comentario_pai',
        'conteudo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function pai(): BelongsTo
    {
        return $this->belongsTo(Comentario::class, 'comentario_pai');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(Comentario::class, 'comentario_pai');
    }
}
