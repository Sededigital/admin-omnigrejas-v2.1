<?php

namespace App\Models\Social;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFollowActivity extends Model
{
    use HasFactory;

    protected $table = 'user_follow_activities';

    protected $fillable = [
        'user_id',
        'activity_type',
        'reference_id',
        'reference_type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Constantes de tipos de atividade
    public const ACTIVITY_POST_CREATED = 'post_created';
    public const ACTIVITY_POST_LIKED = 'post_liked';
    public const ACTIVITY_COMMENT_CREATED = 'comment_created';
    public const ACTIVITY_EVENT_CREATED = 'event_created';
    public const ACTIVITY_MESSAGE_SENT = 'message_sent';

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    /**
     * Usuário que realizou a atividade
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Notificações relacionadas a esta atividade
     */
    public function notifications()
    {
        return $this->hasMany(UserFollowNotification::class, 'activity_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope para atividades de um usuário específico
     */
    public function scopeDoUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para tipo específico de atividade
     */
    public function scopeDoTipo($query, $activityType)
    {
        return $query->where('activity_type', $activityType);
    }

    /**
     * Scope para atividades recentes
     */
    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    /**
     * Scope para atividades relacionadas a um objeto específico
     */
    public function scopeReferencia($query, $referenceId, $referenceType = null)
    {
        $query->where('reference_id', $referenceId);

        if ($referenceType) {
            $query->where('reference_type', $referenceType);
        }

        return $query;
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Obter o tipo de atividade formatado
     */
    public function getTipoFormatado(): string
    {
        return match($this->activity_type) {
            self::ACTIVITY_POST_CREATED => 'Post criado',
            self::ACTIVITY_POST_LIKED => 'Post curtido',
            self::ACTIVITY_COMMENT_CREATED => 'Comentário criado',
            self::ACTIVITY_EVENT_CREATED => 'Evento criado',
            self::ACTIVITY_MESSAGE_SENT => 'Mensagem enviada',
            default => ucfirst(str_replace('_', ' ', $this->activity_type))
        };
    }

    /**
     * Verificar se a atividade é recente (últimas 24 horas)
     */
    public function isRecente(): bool
    {
        return $this->created_at->diffInHours(now()) < 24;
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    /**
     * Registrar uma nova atividade
     */
    public static function registrar(
        $userId,
        $activityType,
        $referenceId = null,
        $referenceType = null,
        $description = null,
        $metadata = []
    ): static {
        return static::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Registrar atividade de criação de post
     */
    public static function registrarPostCriado($userId, $postId, $titulo = null): static
    {
        $description = $titulo ? "Criou um novo post: {$titulo}" : "Criou um novo post";

        return static::registrar(
            $userId,
            self::ACTIVITY_POST_CREATED,
            $postId,
            'post',
            $description,
            ['titulo' => $titulo]
        );
    }

    /**
     * Registrar atividade de curtida em post
     */
    public static function registrarPostCurtido($userId, $postId, $tituloPost = null): static
    {
        $description = $tituloPost ? "Curtiu o post: {$tituloPost}" : "Curtiu um post";

        return static::registrar(
            $userId,
            self::ACTIVITY_POST_LIKED,
            $postId,
            'post',
            $description,
            ['titulo_post' => $tituloPost]
        );
    }

    /**
     * Registrar atividade de criação de comentário
     */
    public static function registrarComentarioCriado($userId, $comentarioId, $postId, $conteudo = null): static
    {
        $description = $conteudo
            ? "Comentou: " . substr($conteudo, 0, 50) . (strlen($conteudo) > 50 ? '...' : '')
            : "Fez um comentário";

        return static::registrar(
            $userId,
            self::ACTIVITY_COMMENT_CREATED,
            $comentarioId,
            'comment',
            $description,
            ['post_id' => $postId, 'conteudo' => $conteudo]
        );
    }

    /**
     * Registrar atividade de criação de evento
     */
    public static function registrarEventoCriado($userId, $eventoId, $titulo = null): static
    {
        $description = $titulo ? "Criou um novo evento: {$titulo}" : "Criou um novo evento";

        return static::registrar(
            $userId,
            self::ACTIVITY_EVENT_CREATED,
            $eventoId,
            'event',
            $description,
            ['titulo' => $titulo]
        );
    }
}
