<?php

namespace App\Models\Pedidos;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Cursos\Curso;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoEspecial extends Model
{
    use HasFactory;

    protected $table = 'pedidos_especiais';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'igreja_id',
        'membro_id',
        'pedido_tipo_id',
        'descricao',
        'curso_id',
        'status',
        'data_pedido',
        'data_resolucao',
        'responsavel_id',
    ];

    protected $casts = [
        'data_pedido' => 'date',
        'data_resolucao' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes para status
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_EM_ANDAMENTO = 'em_andamento';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_REJEITADO = 'rejeitado';
    public const STATUS_CONCLUIDO = 'concluido';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDENTE,
        self::STATUS_EM_ANDAMENTO,
        self::STATUS_APROVADO,
        self::STATUS_REJEITADO,
        self::STATUS_CONCLUIDO,
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    public function pedidoTipo(): BelongsTo
    {
        return $this->belongsTo(PedidoTipo::class, 'pedido_tipo_id');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    // 🔗 RELACIONAMENTOS ATRAVÉS DE OUTRAS MODELS
    public function user()
    {
        return $this->membro->user ?? null;
    }

    public function categoria()
    {
        return $this->pedidoTipo->categoria ?? null;
    }

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    public function isEmAndamento(): bool
    {
        return $this->status === self::STATUS_EM_ANDAMENTO;
    }

    public function isAprovado(): bool
    {
        return $this->status === self::STATUS_APROVADO;
    }

    public function isRejeitado(): bool
    {
        return $this->status === self::STATUS_REJEITADO;
    }

    public function isConcluido(): bool
    {
        return $this->status === self::STATUS_CONCLUIDO;
    }

    public function isResolvido(): bool
    {
        return in_array($this->status, [self::STATUS_APROVADO, self::STATUS_REJEITADO, self::STATUS_CONCLUIDO]);
    }

    public function isAtivo(): bool
    {
        return in_array($this->status, [self::STATUS_PENDENTE, self::STATUS_EM_ANDAMENTO, self::STATUS_APROVADO]);
    }

    public function temCurso(): bool
    {
        return !is_null($this->curso_id);
    }

    public function temResponsavel(): bool
    {
        return !is_null($this->responsavel_id);
    }

    // ========================================
    // Scopes
    // ========================================
    public function scopePendentes($query)
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', self::STATUS_EM_ANDAMENTO);
    }

    public function scopeAprovados($query)
    {
        return $query->where('status', self::STATUS_APROVADO);
    }

    public function scopeRejeitados($query)
    {
        return $query->where('status', self::STATUS_REJEITADO);
    }

    public function scopeConcluidos($query)
    {
        return $query->where('status', self::STATUS_CONCLUIDO);
    }

    public function scopeAtivos($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDENTE, self::STATUS_EM_ANDAMENTO, self::STATUS_APROVADO]);
    }

    public function scopeResolvidos($query)
    {
        return $query->whereIn('status', [self::STATUS_APROVADO, self::STATUS_REJEITADO, self::STATUS_CONCLUIDO]);
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopePorMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('pedido_tipo_id', $tipoId);
    }

    public function scopePorResponsavel($query, $responsavelId)
    {
        return $query->where('responsavel_id', (string) $responsavelId);
    }

    public function scopeComCurso($query)
    {
        return $query->whereNotNull('curso_id');
    }

    public function scopeSemCurso($query)
    {
        return $query->whereNull('curso_id');
    }

    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('data_pedido', '>=', now()->subDays($dias));
    }

    // ========================================
    // Accessors
    // ========================================
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_EM_ANDAMENTO => 'Em Andamento',
            self::STATUS_APROVADO => 'Aprovado',
            self::STATUS_REJEITADO => 'Rejeitado',
            self::STATUS_CONCLUIDO => 'Concluído',
            default => 'Não definido',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDENTE => 'badge-warning',
            self::STATUS_EM_ANDAMENTO => 'badge-info',
            self::STATUS_APROVADO => 'badge-success',
            self::STATUS_REJEITADO => 'badge-danger',
            self::STATUS_CONCLUIDO => 'badge-primary',
            default => 'badge-light',
        };
    }

    public function getNomeCompletoAttribute(): string
    {
        return $this->membro->user->name ?? 'Nome não disponível';
    }

    public function getNomeTipoAttribute(): string
    {
        return $this->pedidoTipo->nome ?? 'Tipo não definido';
    }

    public function getNomeCursoAttribute(): string
    {
        return $this->curso->nome ?? 'Sem curso associado';
    }

    public function getNomeResponsavelAttribute(): string
    {
        return $this->responsavel->name ?? 'Sem responsável';
    }

    public function getDiasAbertoAttribute(): int
    {
        return $this->data_pedido ? \Carbon\Carbon::parse($this->data_pedido)->diffInDays(now()) : 0;
    }

    public function getDiasResolucaoAttribute(): ?int
    {
        if (!$this->data_resolucao || !$this->data_pedido) {
            return null;
        }

        return Carbon::parse($this->data_pedido)->diffInDays(Carbon::parse($this->data_resolucao));
    }

    public function getTempoProcessamentoAttribute(): string
    {
        $dias = $this->dias_aberto;

        if ($dias === 0) {
            return 'Hoje';
        } elseif ($dias === 1) {
            return '1 dia';
        } else {
            return "{$dias} dias";
        }
    }

    // ========================================
    // Métodos de negócio
    // ========================================
    public function iniciarAndamento(?string $responsavelId = null): bool
    {
        if (!$this->isPendente()) {
            return false;
        }

        $this->status = self::STATUS_EM_ANDAMENTO;

        if ($responsavelId) {
            $this->responsavel_id = $responsavelId;
        }

        return $this->save();
    }

    public function aprovar(?string $responsavelId = null, ?string $observacao = null): bool
    {
        if (!in_array($this->status, [self::STATUS_PENDENTE, self::STATUS_EM_ANDAMENTO])) {
            return false;
        }

        $this->status = self::STATUS_APROVADO;
        $this->data_resolucao = now()->toDateString();

        if ($responsavelId) {
            $this->responsavel_id = $responsavelId;
        }

        if ($observacao) {
            $this->descricao = $this->descricao ? $this->descricao . "\n\nAprovação: " . $observacao : "Aprovação: " . $observacao;
        }

        return $this->save();
    }

    public function rejeitar(?string $responsavelId = null, ?string $motivo = null): bool
    {
        if (!in_array($this->status, [self::STATUS_PENDENTE, self::STATUS_EM_ANDAMENTO])) {
            return false;
        }

        $this->status = self::STATUS_REJEITADO;
        $this->data_resolucao = now()->toDateString();

        if ($responsavelId) {
            $this->responsavel_id = $responsavelId;
        }

        if ($motivo) {
            $this->descricao = $this->descricao ? $this->descricao . "\n\nRejeição: " . $motivo : "Rejeição: " . $motivo;
        }

        return $this->save();
    }

    public function concluir(?string $responsavelId = null, ?string $observacao = null): bool
    {
        if (!$this->isAprovado()) {
            return false;
        }

        $this->status = self::STATUS_CONCLUIDO;
        $this->data_resolucao = now()->toDateString();

        if ($responsavelId) {
            $this->responsavel_id = $responsavelId;
        }

        if ($observacao) {
            $this->descricao = $this->descricao ? $this->descricao . "\n\nConclusão: " . $observacao : "Conclusão: " . $observacao;
        }

        return $this->save();
    }

    public function associarCurso(string $cursoId): bool
    {
        $this->curso_id = $cursoId;
        return $this->save();
    }

    public function desassociarCurso(): bool
    {
        $this->curso_id = null;
        return $this->save();
    }

    public function atribuirResponsavel(string $responsavelId): bool
    {
        $this->responsavel_id = $responsavelId;
        return $this->save();
    }

    public function removerResponsavel(): bool
    {
        $this->responsavel_id = null;
        return $this->save();
    }

    // ========================================
    // Métodos estáticos
    // ========================================
    public static function pendentesParaIgreja(int $igrejaId): Collection
    {
        return self::pendentes()->porIgreja($igrejaId)->get();
    }

    public static function estatisticasPorStatus(): array
    {
        return [
            'pendentes' => self::pendentes()->count(),
            'em_andamento' => self::emAndamento()->count(),
            'aprovados' => self::aprovados()->count(),
            'rejeitados' => self::rejeitados()->count(),
            'concluidos' => self::concluidos()->count(),
        ];
    }

    public static function tempoMedioResolucao(): float
    {
        $pedidosResolvidos = self::resolvidos()
            ->whereNotNull('data_resolucao')
            ->get();

        if ($pedidosResolvidos->isEmpty()) {
            return 0;
        }

        $totalDias = $pedidosResolvidos->sum('dias_resolucao');
        return round($totalDias / $pedidosResolvidos->count(), 2);
    }
}
