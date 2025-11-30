<?php

namespace App\Models\Chats;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PostReaction extends Pivot
{
    protected $table = 'post_reactions';
    public $incrementing = false; // chave composta
    protected $primaryKey = null;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'post_id',
        'user_id',
        'reaction',
    ];

    // 🔗 RELACIONAMENTOS
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
