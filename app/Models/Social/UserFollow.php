<?php

namespace App\Models\Social;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFollow extends Model
{
    use HasFactory;

    protected $table = 'user_follows';

    protected $fillable = [
        'follower_id',
        'followed_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes de status
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_BLOQUEADO = 'bloqueado';

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    /**
     * Usuário que está seguindo (follower)
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Usuário que está sendo seguido (followed)
     */
    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope para seguidores ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    /**
     * Scope para seguidores de um usuário específico
     */
    public function scopeSeguidoresDe($query, $userId)
    {
        return $query->where('followed_id', $userId)->ativos();
    }

    /**
     * Scope para usuários seguidos por um usuário específico
     */
    public function scopeSeguidosPor($query, $userId)
    {
        return $query->where('follower_id', $userId)->ativos();
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Verificar se o relacionamento está ativo
     */
    public function isAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    /**
     * Verificar se o relacionamento está bloqueado
     */
    public function isBloqueado(): bool
    {
        return $this->status === self::STATUS_BLOQUEADO;
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    /**
     * Seguir um usuário
     */
    public static function seguir($followerId, $followedId): bool
    {
        // Não permitir seguir a si mesmo
        if ($followerId === $followedId) {
            return false;
        }

        // Verificar se já existe
        $existing = static::where('follower_id', $followerId)
                         ->where('followed_id', $followedId)
                         ->first();

        if ($existing) {
            if ($existing->isAtivo()) {
                return false; // Já está seguindo
            } else {
                // Reativar seguimento bloqueado
                $existing->update(['status' => self::STATUS_ATIVO]);
                return true;
            }
        }

        // Criar novo seguimento
        static::create([
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'status' => self::STATUS_ATIVO,
        ]);

        return true;
    }

    /**
     * Deixar de seguir um usuário
     */
    public static function deixarSeguir($followerId, $followedId): bool
    {
        $follow = static::where('follower_id', $followerId)
                       ->where('followed_id', $followedId)
                       ->where('status', self::STATUS_ATIVO)
                       ->first();

        if ($follow) {
            $follow->update(['status' => self::STATUS_BLOQUEADO]);
            return true;
        }

        return false;
    }

    /**
     * Verificar se um usuário está seguindo outro
     */
    public static function estaSeguindo($followerId, $followedId): bool
    {
        return static::where('follower_id', $followerId)
                    ->where('followed_id', $followedId)
                    ->where('status', self::STATUS_ATIVO)
                    ->exists();
    }

    /**
     * Contar seguidores de um usuário
     */
    public static function contarSeguidores($userId): int
    {
        return static::seguidoresDe($userId)->count();
    }

    /**
     * Contar usuários seguidos por um usuário
     */
    public static function contarSeguidos($userId): int
    {
        return static::seguidosPor($userId)->count();
    }
}
