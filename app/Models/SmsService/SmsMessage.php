<?php

namespace App\Models\SmsService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Igrejas\Igreja;

class SmsMessage extends Model
{
    use HasFactory;

    protected $table = 'sms_messages';

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
        'conversation_id',
        'tipo',
        'conteudo',
        'assunto',
        'remetente_id',
        'destinatario_tipo',
        'igreja_destino_id',
        'status',
        'prioridade',
        'lida_em',
        'respondida_em',
        'anexo_url',
        'anexo_nome',
        'anexo_tamanho',
        'anexo_tipo_mime',
        'anexo_extensao',
        'duracao_audio',
        'dimensoes_imagem',
        'paginas_documento',
        'resposta_para',
        'thread_id',
        'enviada_em',
    ];

    protected $casts = [
        'lida_em' => 'datetime',
        'respondida_em' => 'datetime',
        'enviada_em' => 'datetime',
        'anexo_tamanho' => 'integer',
        'duracao_audio' => 'integer',
        'paginas_documento' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipos de mensagem
    const TIPO_TEXTO = 'texto';
    const TIPO_ARQUIVO = 'arquivo';
    const TIPO_IMAGEM = 'imagem';
    const TIPO_VIDEO = 'video';
    const TIPO_AUDIO = 'audio';
    const TIPO_DOCUMENTO = 'documento';

    // Status da mensagem
    const STATUS_ENVIADA = 'enviada';
    const STATUS_ENTREGUE = 'entregue';
    const STATUS_LIDA = 'lida';
    const STATUS_RESPONDIDA = 'respondida';
    const STATUS_ARQUIVADA = 'arquivada';

    // Prioridades
    const PRIORIDADE_BAIXA = 'baixa';
    const PRIORIDADE_NORMAL = 'normal';
    const PRIORIDADE_ALTA = 'alta';
    const PRIORIDADE_URGENTE = 'urgente';

    // Tipos de destinatário
    const DESTINATARIO_IGREJA_ADMIN = 'igreja_admin';
    const DESTINATARIO_SUPER_ADMIN = 'super_admin';

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SmsConversation::class, 'conversation_id');
    }

    public function remetente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function igrejaDestino(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_destino_id');
    }

    public function respostaPara(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'resposta_para');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'resposta_para');
    }

    public function leituras(): HasMany
    {
        return $this->hasMany(SmsMessageRead::class, 'message_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(SmsAttachment::class, 'message_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(SmsNotification::class, 'message_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeDaConversa($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeDoRemetente($query, $remetenteId)
    {
        return $query->where('remetente_id', $remetenteId);
    }

    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeComStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeComPrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    public function scopeParaIgreja($query, $igrejaId)
    {
        return $query->where('igreja_destino_id', $igrejaId);
    }

    public function scopeDoThread($query, $threadId)
    {
        return $query->where('thread_id', $threadId);
    }

    public function scopeComAnexo($query)
    {
        return $query->whereNotNull('anexo_url');
    }

    public function scopeSemAnexo($query)
    {
        return $query->whereNull('anexo_url');
    }

    public function scopeLidas($query)
    {
        return $query->where('status', self::STATUS_LIDA);
    }

    public function scopeNaoLidas($query)
    {
        return $query->whereIn('status', [self::STATUS_ENVIADA, self::STATUS_ENTREGUE]);
    }

    public function scopeRespondidas($query)
    {
        return $query->where('status', self::STATUS_RESPONDIDA);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('enviada_em', '>=', now()->subDays($dias));
    }

    // ========================================
    // HELPERS
    // ========================================

    public function foiEnviada(): bool
    {
        return in_array($this->status, [self::STATUS_ENVIADA, self::STATUS_ENTREGUE, self::STATUS_LIDA, self::STATUS_RESPONDIDA]);
    }

    public function foiEntregue(): bool
    {
        return in_array($this->status, [self::STATUS_ENTREGUE, self::STATUS_LIDA, self::STATUS_RESPONDIDA]);
    }

    public function foiLida(): bool
    {
        // Verificar diretamente no banco se existe leitura para o usuário atual
        return DB::table('sms_message_reads')
            ->where('message_id', $this->id)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function foiRespondida(): bool
    {
        return $this->status === self::STATUS_RESPONDIDA;
    }

    public function temAnexo(): bool
    {
        return !is_null($this->anexo_url);
    }

    public function ehResposta(): bool
    {
        return !is_null($this->resposta_para);
    }

    public function ehDoUsuarioAtual(): bool
    {
        return $this->remetente_id === Auth::id();
    }

    public function podeSerLida(): bool
    {
        return in_array($this->status, [self::STATUS_ENVIADA, self::STATUS_ENTREGUE]);
    }

    public function podeSerRespondida(): bool
    {
        return $this->foiLida();
    }

    public function marcarComoLida(): void
    {
        if ($this->podeSerLida()) {
            $this->update([
                'status' => self::STATUS_LIDA,
                'lida_em' => now(),
            ]);
        }
    }

    public function marcarComoEntregue(): void
    {
        if ($this->status === self::STATUS_ENVIADA) {
            $this->update([
                'status' => self::STATUS_ENTREGUE,
            ]);
        }
    }

    public function marcarComoRespondida(): void
    {
        if ($this->podeSerRespondida()) {
            $this->update([
                'status' => self::STATUS_RESPONDIDA,
                'respondida_em' => now(),
            ]);
        }
    }

    public function arquivar(): void
    {
        $this->update(['status' => self::STATUS_ARQUIVADA]);
    }

    public function getConteudoResumido($limite = 100): string
    {
        if (!$this->conteudo) {
            return $this->temAnexo() ? 'Mensagem com anexo' : 'Mensagem vazia';
        }

        return strlen($this->conteudo) > $limite
            ? substr($this->conteudo, 0, $limite) . '...'
            : $this->conteudo;
    }

    public function getTempoDecorrido(): string
    {
        return $this->enviada_em ? $this->enviada_em->diffForHumans() : 'Não enviado';
    }

    public function getTipoLabel(): string
    {
        return match($this->tipo) {
            self::TIPO_TEXTO => 'Texto',
            self::TIPO_IMAGEM => 'Imagem',
            self::TIPO_VIDEO => 'Vídeo',
            self::TIPO_AUDIO => 'Áudio',
            self::TIPO_DOCUMENTO => 'Documento',
            self::TIPO_ARQUIVO => 'Arquivo',
            default => 'Desconhecido'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_ENVIADA => 'Enviada',
            self::STATUS_ENTREGUE => 'Entregue',
            self::STATUS_LIDA => 'Lida',
            self::STATUS_RESPONDIDA => 'Respondida',
            self::STATUS_ARQUIVADA => 'Arquivada',
            default => 'Desconhecido'
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_ENVIADA => 'info',
            self::STATUS_ENTREGUE => 'primary',
            self::STATUS_LIDA => 'success',
            self::STATUS_RESPONDIDA => 'warning',
            self::STATUS_ARQUIVADA => 'secondary',
            default => 'light'
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

    public function getDestinatarioLabel(): string
    {
        return match($this->destinatario_tipo) {
            self::DESTINATARIO_SUPER_ADMIN => 'Super Admin',
            self::DESTINATARIO_IGREJA_ADMIN => 'Admin da Igreja',
            default => 'Desconhecido'
        };
    }

    public function getTamanhoAnexoFormatado(): string
    {
        if (!$this->anexo_tamanho) {
            return '';
        }

        $bytes = $this->anexo_tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function getTipoOptions(): array
    {
        return [
            self::TIPO_TEXTO => 'Texto',
            self::TIPO_IMAGEM => 'Imagem',
            self::TIPO_VIDEO => 'Vídeo',
            self::TIPO_AUDIO => 'Áudio',
            self::TIPO_DOCUMENTO => 'Documento',
            self::TIPO_ARQUIVO => 'Arquivo',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ENVIADA => 'Enviada',
            self::STATUS_ENTREGUE => 'Entregue',
            self::STATUS_LIDA => 'Lida',
            self::STATUS_RESPONDIDA => 'Respondida',
            self::STATUS_ARQUIVADA => 'Arquivada',
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

    public static function getDestinatarioOptions(): array
    {
        return [
            self::DESTINATARIO_SUPER_ADMIN => 'Super Admin',
            self::DESTINATARIO_IGREJA_ADMIN => 'Admin da Igreja',
        ];
    }

    public static function getMensagensNaoLidas($userId = null): \Illuminate\Support\Collection
    {
        $query = self::naoLidas();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('remetente_id', '!=', $userId)
                  ->orWhere('igreja_destino_id', function ($subQuery) use ($userId) {
                      $subQuery->select('igreja_id')
                               ->from('igreja_membros')
                               ->where('user_id', $userId)
                               ->limit(1);
                  });
            });
        }

        return $query->with(['conversation', 'remetente'])
                    ->orderBy('enviada_em', 'desc')
                    ->get();
    }

    public static function getMensagensRecentes($conversationId, $limite = 50): \Illuminate\Support\Collection
    {
        return self::daConversa($conversationId)
            ->with(['remetente', 'anexos'])
            ->orderBy('enviada_em', 'desc')
            ->limit($limite)
            ->get()
            ->reverse(); // Para manter ordem cronológica
    }
}
