<?php

namespace App\Models\Cursos;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CursoTurma extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'curso_turmas';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'curso_id',
        'nome',
        'codigo',
        'data_inicio',
        'data_fim',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
        'local',
        'vagas_maximo',
        'vagas_ocupadas',
        'status',
        'instrutor_id',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
        'dia_semana' => 'integer',
        'vagas_maximo' => 'integer',
        'vagas_ocupadas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para status (usando as mesmas do Curso)
    public const STATUS_PLANEJADO = 'planejado';
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_CONCLUIDO = 'concluido';
    public const STATUS_SUSPENSO = 'suspenso';
    public const STATUS_CANCELADO = 'cancelado';

    public const STATUS_OPTIONS = [
        self::STATUS_PLANEJADO,
        self::STATUS_ATIVO,
        self::STATUS_CONCLUIDO,
        self::STATUS_SUSPENSO,
        self::STATUS_CANCELADO,
    ];

    // Constantes para dias da semana
    public const DOMINGO = 0;
    public const SEGUNDA = 1;
    public const TERCA = 2;
    public const QUARTA = 3;
    public const QUINTA = 4;
    public const SEXTA = 5;
    public const SABADO = 6;

    public const DIAS_SEMANA = [
        self::DOMINGO => 'Domingo',
        self::SEGUNDA => 'Segunda-feira',
        self::TERCA => 'Terça-feira',
        self::QUARTA => 'Quarta-feira',
        self::QUINTA => 'Quinta-feira',
        self::SEXTA => 'Sexta-feira',
        self::SABADO => 'Sábado',
    ];

    // 🔗 RELACIONAMENTOS
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function instrutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instrutor_id');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(CursoMatricula::class, 'turma_id');
    }

    // 🔗 RELACIONAMENTOS ESPECIAIS
    public function matriculasAtivas(): HasMany
    {
        return $this->matriculas()->where('status', 'ativo');
    }

    public function matriculasConcluidas(): HasMany
    {
        return $this->matriculas()->where('status', 'concluido');
    }

    public function matriculasDesistentes(): HasMany
    {
        return $this->matriculas()->where('status', 'desistente');
    }

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isPlanejada(): bool
    {
        return $this->status === self::STATUS_PLANEJADO;
    }

    public function isAtiva(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function isConcluida(): bool
    {
        return $this->status === self::STATUS_CONCLUIDO;
    }

    public function isSuspensa(): bool
    {
        return $this->status === self::STATUS_SUSPENSO;
    }

    public function isCancelada(): bool
    {
        return $this->status === self::STATUS_CANCELADO;
    }

    public function temVagasDisponiveis(): bool
    {
        return $this->vagas_ocupadas < $this->vagas_maximo;
    }

    public function isLotada(): bool
    {
        return $this->vagas_ocupadas >= $this->vagas_maximo;
    }

    // ========================================
    // Scopes
    // ========================================
    public function scopeAtivas($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    public function scopePlanejadas($query)
    {
        return $query->where('status', self::STATUS_PLANEJADO);
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', self::STATUS_CONCLUIDO);
    }

    public function scopeComVagas($query)
    {
        return $query->whereRaw('vagas_ocupadas < vagas_maximo');
    }

    public function scopePorDiaSemana($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    public function scopePorCurso($query, $cursoId)
    {
        return $query->where('curso_id', $cursoId);
    }

    // ========================================
    // Accessors
    // ========================================
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PLANEJADO => 'Planejada',
            self::STATUS_ATIVO => 'Ativa',
            self::STATUS_CONCLUIDO => 'Concluída',
            self::STATUS_SUSPENSO => 'Suspensa',
            self::STATUS_CANCELADO => 'Cancelada',
            default => 'Não definido',
        };
    }

    public function getDiaSemanaLabelAttribute(): string
    {
        return self::DIAS_SEMANA[$this->dia_semana] ?? 'Não definido';
    }

    public function getHorarioFormatadoAttribute(): string
    {
        if ($this->hora_inicio && $this->hora_fim) {
            return "{$this->hora_inicio->format('H:i')} às {$this->hora_fim->format('H:i')}";
        }
        return 'Não definido';
    }

    public function getVagasDisponiveisAttribute(): int
    {
        return max(0, $this->vagas_maximo - $this->vagas_ocupadas);
    }

    public function getPercentualOcupacaoAttribute(): float
    {
        if ($this->vagas_maximo === 0) {
            return 0;
        }
        return round(($this->vagas_ocupadas / $this->vagas_maximo) * 100, 2);
    }

    public function getNomeCompletoAttribute(): string
    {
        return $this->codigo ? "{$this->nome} ({$this->codigo})" : $this->nome;
    }

    // ========================================
    // Métodos de negócio
    // ========================================
    public function adicionarMatricula(): bool
    {
        if ($this->temVagasDisponiveis()) {
            $this->increment('vagas_ocupadas');
            return true;
        }
        return false;
    }

    public function removerMatricula(): bool
    {
        if ($this->vagas_ocupadas > 0) {
            $this->decrement('vagas_ocupadas');
            return true;
        }
        return false;
    }

    public function atualizarVagasOcupadas(): void
    {
        $this->vagas_ocupadas = $this->matriculasAtivas()->count();
        $this->save();
    }
}
