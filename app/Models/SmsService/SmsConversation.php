<?php

namespace App\Models\SmsService;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsConversation extends Model
{
    use HasFactory;

    protected $table = 'sms_conversations';

    /**
     * Indicates if the model's ID is auto-incrementing.
     * Set to false since we use UUIDs
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    protected $fillable = [
        'titulo',
        'descricao',
        'igreja_id',
        'iniciada_por',
        'status',
        'prioridade',
        'categoria',
        'primeira_mensagem_em',
        'ultima_mensagem_em',
        'resolvida_em',
        'resolvida_por',
        'tags',
    ];

    protected $casts = [
        'primeira_mensagem_em' => 'datetime',
        'ultima_mensagem_em' => 'datetime',
        'resolvida_em' => 'datetime',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status possíveis
    const STATUS_ATIVA = 'ativa';
    const STATUS_ARQUIVADA = 'arquivada';
    const STATUS_FECHADA = 'fechada';

    // Prioridades
    const PRIORIDADE_BAIXA = 'baixa';
    const PRIORIDADE_NORMAL = 'normal';
    const PRIORIDADE_ALTA = 'alta';
    const PRIORIDADE_URGENTE = 'urgente';

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function iniciadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iniciada_por');
    }

    public function resolvidaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolvida_por');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'conversation_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeDaIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeComStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeComPrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    public function scopeDaCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeAtivas($query)
    {
        return $query->where('status', self::STATUS_ATIVA);
    }

    public function scopeArquivadas($query)
    {
        return $query->where('status', self::STATUS_ARQUIVADA);
    }

    public function scopeFechadas($query)
    {
        return $query->where('status', self::STATUS_FECHADA);
    }

    public function scopeResolvidas($query)
    {
        return $query->whereNotNull('resolvida_em');
    }

    public function scopeNaoResolvidas($query)
    {
        return $query->whereNull('resolvida_em');
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // ========================================
    // HELPERS
    // ========================================

    public function estaAtiva(): bool
    {
        return $this->status === self::STATUS_ATIVA;
    }

    public function estaArquivada(): bool
    {
        return $this->status === self::STATUS_ARQUIVADA;
    }

    public function estaFechada(): bool
    {
        return $this->status === self::STATUS_FECHADA;
    }

    public function estaResolvida(): bool
    {
        return !is_null($this->resolvida_em);
    }

    public function podeSerArquivada(): bool
    {
        return $this->status === self::STATUS_ATIVA;
    }

    public function podeSerFechada(): bool
    {
        return $this->status === self::STATUS_ATIVA;
    }

    public function podeSerReaberta(): bool
    {
        return in_array($this->status, [self::STATUS_ARQUIVADA, self::STATUS_FECHADA]);
    }

    public function getTotalMensagens(): int
    {
        // Verificar se a conversa tem ID válido
        if (!$this->id) {
            return 0;
        }

        return $this->mensagens()->count();
    }

    public function getMensagensNaoLidas($userId = null): int
    {
        // Verificar se a conversa tem ID válido
        if (!$this->id) {
            return 0;
        }

        $userId = $userId ?? Auth::id();

        return $this->mensagens()
            ->leftJoin('sms_message_reads', function($join) use ($userId) {
                $join->on('sms_messages.id', '=', 'sms_message_reads.message_id')
                     ->where('sms_message_reads.user_id', '=', $userId);
            })
            ->whereNull('sms_message_reads.id')
            ->whereIn('sms_messages.status', ['enviada', 'entregue'])
            ->count();
    }

    public function getUltimaMensagem()
    {
        // Verificar se a conversa tem ID válido
        if (!$this->id) {
            return null;
        }

        return $this->mensagens()
            ->with('remetente')
            ->orderBy('enviada_em', 'desc')
            ->first();
    }

    public function getPreviewUltimaMensagem($limite = 100): string
    {
        $ultimaMensagem = $this->getUltimaMensagem();

        if (!$ultimaMensagem) {
            return 'Nenhuma mensagem';
        }

        $conteudo = $ultimaMensagem->conteudo ?: 'Mensagem com anexo';

        return strlen($conteudo) > $limite
            ? substr($conteudo, 0, $limite) . '...'
            : $conteudo;
    }

    public function arquivar(): void
    {
        if ($this->podeSerArquivada()) {
            $this->update(['status' => self::STATUS_ARQUIVADA]);
        }
    }

    public function fechar(): void
    {
        if ($this->podeSerFechada()) {
            $this->update([
                'status' => self::STATUS_FECHADA,
                'resolvida_em' => now(),
            ]);
        }
    }

    public function reabrir(): void
    {
        if ($this->podeSerReaberta()) {
            $this->update([
                'status' => self::STATUS_ATIVA,
                'resolvida_em' => null,
            ]);
        }
    }

    public function resolver(User $resolvidoPor): void
    {
        $this->update([
            'resolvida_em' => now(),
            'resolvida_por' => $resolvidoPor->id,
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_ATIVA => 'success',
            self::STATUS_ARQUIVADA => 'warning',
            self::STATUS_FECHADA => 'secondary',
            default => 'light'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_ATIVA => 'Ativa',
            self::STATUS_ARQUIVADA => 'Arquivada',
            self::STATUS_FECHADA => 'Fechada',
            default => 'Desconhecido'
        };
    }

    public function getPrioridadeBadgeClass(): string
    {
        return match($this->prioridade) {
            self::PRIORIDADE_BAIXA => 'light',
            self::PRIORIDADE_NORMAL => 'info',
            self::PRIORIDADE_ALTA => 'warning',
            self::PRIORIDADE_URGENTE => 'danger',
            default => 'secondary'
        };
    }

    public function getPrioridadeLabel(): string
    {
        return match($this->prioridade) {
            self::PRIORIDADE_BAIXA => 'Baixa',
            self::PRIORIDADE_NORMAL => 'Normal',
            self::PRIORIDADE_ALTA => 'Alta',
            self::PRIORIDADE_URGENTE => 'Urgente',
            default => 'Normal'
        };
    }

    public function getTempoDecorrido(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getTempoUltimaMensagem(): string
    {
        return $this->ultima_mensagem_em ? $this->ultima_mensagem_em->diffForHumans() : 'Nunca';
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ATIVA => 'Ativa',
            self::STATUS_ARQUIVADA => 'Arquivada',
            self::STATUS_FECHADA => 'Fechada',
        ];
    }

    public static function getPrioridadeOptions(): array
    {
        return [
            self::PRIORIDADE_BAIXA => 'Baixa',
            self::PRIORIDADE_NORMAL => 'Normal',
            self::PRIORIDADE_ALTA => 'Alta',
            self::PRIORIDADE_URGENTE => 'Urgente',
        ];
    }

    public static function getConversasAtivas(): \Illuminate\Support\Collection
    {
        return self::ativas()
            ->with(['igreja', 'iniciadaPor'])
            ->orderBy('ultima_mensagem_em', 'desc')
            ->get();
    }

    public static function getConversasPorIgreja($igrejaId): \Illuminate\Support\Collection
    {
        return self::daIgreja($igrejaId)
            ->with(['iniciadaPor', 'resolvidaPor'])
            ->orderBy('ultima_mensagem_em', 'desc')
            ->get();
    }
}
