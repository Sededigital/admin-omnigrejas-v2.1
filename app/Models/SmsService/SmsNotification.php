<?php

namespace App\Models\SmsService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class SmsNotification extends Model
{
    use HasFactory;

    protected $table = 'sms_notifications';

    protected $fillable = [
        'message_id',
        'user_id',
        'tipo',
        'titulo',
        'mensagem',
        'enviada',
        'enviada_em',
        'lida',
        'lida_em',
        'dados_extras',
    ];

    protected $casts = [
        'enviada' => 'boolean',
        'enviada_em' => 'datetime',
        'lida' => 'boolean',
        'lida_em' => 'datetime',
        'dados_extras' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipos de notificação
    const TIPO_PUSH = 'push';
    const TIPO_EMAIL = 'email';
    const TIPO_SMS = 'sms';

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    public function message(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeDaMensagem($query, $messageId)
    {
        return $query->where('message_id', $messageId);
    }

    public function scopeDoUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeEnviadas($query)
    {
        return $query->where('enviada', true);
    }

    public function scopeNaoEnviadas($query)
    {
        return $query->where('enviada', false);
    }

    public function scopeLidas($query)
    {
        return $query->where('lida', true);
    }

    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopePush($query)
    {
        return $query->where('tipo', self::TIPO_PUSH);
    }

    public function scopeEmail($query)
    {
        return $query->where('tipo', self::TIPO_EMAIL);
    }

    public function scopeSms($query)
    {
        return $query->where('tipo', self::TIPO_SMS);
    }

    // ========================================
    // HELPERS
    // ========================================

    public function foiEnviada(): bool
    {
        return $this->enviada;
    }

    public function foiLida(): bool
    {
        return $this->lida;
    }

    public function podeSerEnviada(): bool
    {
        return !$this->enviada;
    }

    public function podeSerLida(): bool
    {
        return $this->enviada && !$this->lida;
    }

    public function ehPush(): bool
    {
        return $this->tipo === self::TIPO_PUSH;
    }

    public function ehEmail(): bool
    {
        return $this->tipo === self::TIPO_EMAIL;
    }

    public function ehSms(): bool
    {
        return $this->tipo === self::TIPO_SMS;
    }

    public function marcarComoEnviada(): void
    {
        if ($this->podeSerEnviada()) {
            $this->update([
                'enviada' => true,
                'enviada_em' => now(),
            ]);
        }
    }

    public function marcarComoLida(): void
    {
        if ($this->podeSerLida()) {
            $this->update([
                'lida' => true,
                'lida_em' => now(),
            ]);
        }
    }

    public function getTipoLabel(): string
    {
        return match($this->tipo) {
            self::TIPO_PUSH => 'Push',
            self::TIPO_EMAIL => 'Email',
            self::TIPO_SMS => 'SMS',
            default => 'Desconhecido'
        };
    }

    public function getStatusLabel(): string
    {
        if (!$this->enviada) {
            return 'Pendente';
        }

        return $this->lida ? 'Lida' : 'Enviada';
    }

    public function getStatusBadgeClass(): string
    {
        if (!$this->enviada) {
            return 'warning';
        }

        return $this->lida ? 'success' : 'info';
    }

    public function getTempoDecorrido(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getTempoEnvio(): string
    {
        return $this->enviada_em ? $this->enviada_em->diffForHumans() : 'Não enviado';
    }

    public function getTempoLeitura(): string
    {
        return $this->lida_em ? $this->lida_em->diffForHumans() : 'Não lida';
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function getTipoOptions(): array
    {
        return [
            self::TIPO_PUSH => 'Push Notification',
            self::TIPO_EMAIL => 'Email',
            self::TIPO_SMS => 'SMS',
        ];
    }

    public static function getNotificacoesNaoLidas($userId): \Illuminate\Support\Collection
    {
        return self::doUsuario($userId)
            ->naoLidas()
            ->enviadas()
            ->with(['message.conversation'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getNotificacoesPendentes(): \Illuminate\Support\Collection
    {
        return self::naoEnviadas()
            ->with(['message', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public static function marcarTodasComoLidas($userId): int
    {
        return self::doUsuario($userId)
            ->naoLidas()
            ->enviadas()
            ->update([
                'lida' => true,
                'lida_em' => now(),
            ]);
    }

    public static function getEstatisticasPorTipo(): \Illuminate\Support\Collection
    {
        return self::selectRaw('
                tipo,
                COUNT(*) as total,
                COUNT(CASE WHEN enviada THEN 1 END) as enviadas,
                COUNT(CASE WHEN lida THEN 1 END) as lidas
            ')
            ->groupBy('tipo')
            ->get();
    }

    public static function getNotificacoesRecentes($userId, $limite = 20): \Illuminate\Support\Collection
    {
        return self::doUsuario($userId)
            ->with(['message.conversation'])
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }
}