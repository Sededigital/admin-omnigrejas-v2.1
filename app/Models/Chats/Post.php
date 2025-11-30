<?php

namespace App\Models\Chats;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Chats\Comentario;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $primaryKey = 'id';
    public $incrementing = true; // BIGSERIAL
    protected $keyType = 'int';

    protected $fillable = [
        'igreja_id',
        'author_id',
        'titulo',
        'content',
        'media_url',
        'media_nome',
        'media_tamanho',
        'media_mime_type',
        'media_type',
        'is_video',
    ];

    protected $casts = [
        'is_video' => 'boolean',
        'media_tamanho' => 'integer',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function reactions()
    {
        return $this->hasMany(PostReaction::class, 'post_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'post_id');
    }

}
