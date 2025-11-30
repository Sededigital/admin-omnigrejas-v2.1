<?php

namespace App\Models\Cursos;

use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CursoMatricula extends Model
{
    use HasFactory;

    protected $table = 'curso_matriculas';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'turma_id',
        'membro_id',
        'data_matricula',
        'status',
        'apto',
        'data_apto',
        'certificado_emitido',
        'data_certificado',
        'observacoes',
    ];

    protected $casts = [
        'data_matricula' => 'date',
        'data_apto' => 'date',
        'data_certificado' => 'date',
        'apto' => 'boolean',
        'certificado_emitido' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes para status
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_CONCLUIDO = 'concluido';
    public const STATUS_DESISTENTE = 'desistente';
    public const STATUS_TRANSFERIDO = 'transferido';
    public const STATUS_SUSPENSO = 'suspenso';

    public const STATUS_OPTIONS = [
        self::STATUS_ATIVO,
        self::STATUS_CONCLUIDO,
        self::STATUS_DESISTENTE,
        self::STATUS_TRANSFERIDO,
        self::STATUS_SUSPENSO,
    ];

    // 🔗 RELACIONAMENTOS
    public function turma(): BelongsTo
    {
        return $this->belongsTo(CursoTurma::class, 'turma_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    public function certificado(): HasOne
    {
        return $this->hasOne(CursoCertificado::class, 'matricula_id');
    }

    // 🔗 RELACIONAMENTOS ATRAVÉS DE OUTRAS MODELS
    public function curso()
    {
        return $this->turma->curso ?? null;
    }

    public function igreja()
    {
        return $this->membro->igreja ?? null;
    }

    public function user()
    {
        return $this->membro->user ?? null;
    }

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isAtiva(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function isConcluida(): bool
    {
        return $this->status === self::STATUS_CONCLUIDO;
    }

    public function isDesistente(): bool
    {
        return $this->status === self::STATUS_DESISTENTE;
    }

    public function isTransferida(): bool
    {
        return $this->status === self::STATUS_TRANSFERIDO;
    }

    public function isSuspensa(): bool
    {
        return $this->status === self::STATUS_SUSPENSO;
    }

    public function isApto(): bool
    {
        return $this->apto === true;
    }

    public function isCertificadoEmitido(): bool
    {
        return $this->certificado_emitido === true;
    }

    public function temCertificado(): bool
    {
        return $this->certificado()->exists();
    }

    // ========================================
    // Scopes
    // ========================================
    public function scopeAtivas($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', self::STATUS_CONCLUIDO);
    }

    public function scopeDesistentes($query)
    {
        return $query->where('status', self::STATUS_DESISTENTE);
    }

    public function scopeAptos($query)
    {
        return $query->where('apto', true);
    }

    public function scopeComCertificado($query)
    {
        return $query->where('certificado_emitido', true);
    }

    public function scopeSemCertificado($query)
    {
        return $query->where('certificado_emitido', false);
    }

    public function scopePorTurma($query, $turmaId)
    {
        return $query->where('turma_id', $turmaId);
    }

    public function scopePorMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // ========================================
    // Accessors
    // ========================================
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_CONCLUIDO => 'Concluído',
            self::STATUS_DESISTENTE => 'Desistente',
            self::STATUS_TRANSFERIDO => 'Transferido',
            self::STATUS_SUSPENSO => 'Suspenso',
            default => 'Não definido',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ATIVO => 'badge-success',
            self::STATUS_CONCLUIDO => 'badge-primary',
            self::STATUS_DESISTENTE => 'badge-danger',
            self::STATUS_TRANSFERIDO => 'badge-warning',
            self::STATUS_SUSPENSO => 'badge-secondary',
            default => 'badge-light',
        };
    }

    public function getNomeCompletoAttribute(): string
    {
        return $this->membro->user->name ?? 'Nome não disponível';
    }

    public function getNomeCursoAttribute(): string
    {
        return $this->turma->curso->nome ?? 'Curso não disponível';
    }

    public function getNomeTurmaAttribute(): string
    {
        return $this->turma->nome ?? 'Turma não disponível';
    }

    public function getDiasMatriculadoAttribute(): int
    {
        return $this->data_matricula ? \Carbon\Carbon::parse($this->data_matricula)->diffInDays(now()) : 0;
    }

    // ========================================
    // Métodos de negócio
    // ========================================
    public function marcarComoApto(string $observacao = null): bool
    {
        $this->apto = true;
        $this->data_apto = now()->toDateString();

        if ($observacao) {
            $this->observacoes = $this->observacoes ? $this->observacoes . "\n" . $observacao : $observacao;
        }

        return $this->save();
    }

    public function marcarComoNaoApto(string $observacao = null): bool
    {
        $this->apto = false;
        $this->data_apto = null;

        if ($observacao) {
            $this->observacoes = $this->observacoes ? $this->observacoes . "\n" . $observacao : $observacao;
        }

        return $this->save();
    }

    public function concluir(string $observacao = null): bool
    {
        $this->status = self::STATUS_CONCLUIDO;

        if ($observacao) {
            $this->observacoes = $this->observacoes ? $this->observacoes . "\n" . $observacao : $observacao;
        }

        return $this->save();
    }

    public function marcarDesistencia(string $motivo = null): bool
    {
        $this->status = self::STATUS_DESISTENTE;

        if ($motivo) {
            $observacao = "Desistência: " . $motivo;
            $this->observacoes = $this->observacoes ? $this->observacoes . "\n" . $observacao : $observacao;
        }

        // Atualizar vagas da turma
        $this->turma->removerMatricula();

        return $this->save();
    }

    public function emitirCertificado(): bool
    {
        if (!$this->isApto() || !$this->isConcluida()) {
            return false;
        }

        $this->certificado_emitido = true;
        $this->data_certificado = now()->toDateString();

        return $this->save();
    }

    public function adicionarObservacao(string $observacao): bool
    {
        $novaObservacao = "[" . now()->format('d/m/Y H:i') . "] " . $observacao;
        $this->observacoes = $this->observacoes ? $this->observacoes . "\n" . $novaObservacao : $novaObservacao;

        return $this->save();
    }
}
