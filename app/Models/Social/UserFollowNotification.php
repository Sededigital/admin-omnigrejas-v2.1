<?php

namespace App\Models\Social;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFollowNotification extends Model
{
    use HasFactory;

    protected $table = 'user_follow_notifications';

    protected $fillable = [
        'follower_id',
        'followed_id',
        'activity_id',
        'notification_type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    /**
     * Usuário que recebe a notificação (follower)
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Usuário que realizou a atividade (followed)
     */
    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id');
    }

    /**
     * Atividade relacionada à notificação
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(UserFollowActivity::class, 'activity_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope para notificações não lidas
     */
    public function scopeNaoLidas($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope para notificações lidas
     */
    public function scopeLidas($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope para notificações de um usuário específico
     */
    public function scopeDoUsuario($query, $userId)
    {
        return $query->where('follower_id', $userId);
    }

    /**
     * Scope para notificações de um tipo específico
     */
    public function scopeDoTipo($query, $notificationType)
    {
        return $query->where('notification_type', $notificationType);
    }

    /**
     * Scope para notificações recentes
     */
    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Marcar notificação como lida
     */
    public function marcarComoLida(): bool
    {
        if ($this->is_read) {
            return false; // Já está lida
        }

        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Marcar notificação como não lida
     */
    public function marcarComoNaoLida(): bool
    {
        if (!$this->is_read) {
            return false; // Já está não lida
        }

        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Verificar se a notificação foi lida
     */
    public function foiLida(): bool
    {
        return $this->is_read;
    }

    /**
     * Verificar se a notificação é recente (últimas 24 horas)
     */
    public function isRecente(): bool
    {
        return $this->created_at->diffInHours(now()) < 24;
    }

    /**
     * Obter tempo decorrido desde a criação
     */
    public function getTempoDecorrido(): string
    {
        return $this->created_at->diffForHumans();
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    /**
     * Criar uma nova notificação
     */
    public static function criar(
        $followerId,
        $followedId,
        $activityId,
        $notificationType,
        $title,
        $message,
        $data = []
    ): static {
        return static::create([
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'activity_id' => $activityId,
            'notification_type' => $notificationType,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Contar notificações não lidas de um usuário
     */
    public static function contarNaoLidas($userId): int
    {
        return static::doUsuario($userId)->naoLidas()->count();
    }

    /**
     * Marcar todas as notificações de um usuário como lidas
     */
    public static function marcarTodasComoLidas($userId): int
    {
        return static::doUsuario($userId)
                    ->naoLidas()
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
    }

    /**
     * Obter notificações recentes de um usuário
     */
    public static function obterRecentes($userId, $limite = 50): \Illuminate\Database\Eloquent\Collection
    {
        return static::doUsuario($userId)
                    ->with(['followed', 'activity'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limite)
                    ->get();
    }
}
