<?php

namespace App\Models\Chats;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaChatParticipante extends Model
{
    use HasFactory;

    protected $table = 'igreja_chat_participantes';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'chat_id',
        'user_id',
        'is_admin',
        'added_by',
        'data_entrada',
        'status',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'data_entrada' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    public function chat(): BelongsTo
    {
        return $this->belongsTo(IgrejaChat::class, 'chat_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function adicionadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // ========================================
    // HELPERS PARA STATUS
    // ========================================

    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function isRemovido(): bool
    {
        return $this->status === 'removido';
    }

    public function isSaiu(): bool
    {
        return $this->status === 'saiu';
    }

    // ========================================
    // HELPERS PARA ADMIN
    // ========================================

    public function isAdminGrupo(): bool
    {
        return $this->is_admin === true;
    }

    public function promoverParaAdmin(): bool
    {
        $this->is_admin = true;
        return $this->save();
    }

    public function removerComoAdmin(): bool
    {
        // Não permitir remover admin do criador do chat
        if ($this->user_id === $this->chat->created_by) {
            return false;
        }

        $this->is_admin = false;
        return $this->save();
    }

    public function podeGerenciarAdmins(): bool
    {
        // Criador do chat sempre pode gerenciar admins
        if ($this->user_id === $this->chat->created_by) {
            return true;
        }

        // Admins do grupo podem gerenciar outros admins
        return $this->isAdminGrupo();
    }

    // ========================================
    // AÇÕES DO PARTICIPANTE
    // ========================================

    public function sairDoGrupo(): bool
    {
        $this->status = 'saiu';
        return $this->save();
    }

    public function removerDoGrupo(): bool
    {
        $this->status = 'removido';
        return $this->save();
    }

    public function reativarNoGrupo(): bool
    {
        $this->status = 'ativo';
        return $this->save();
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopePorChat($query, $chatId)
    {
        return $query->where('chat_id', $chatId);
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
