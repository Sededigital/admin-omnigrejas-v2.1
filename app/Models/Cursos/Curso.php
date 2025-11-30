<?php

namespace App\Models\Cursos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Igrejas\Igreja;
use App\Models\User;
use App\Models\Cursos\CursoTurma;
use App\Models\PedidoEspecial;

class Curso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cursos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'igreja_id',
        'nome',
        'tipo',
        'descricao',
        'objetivo',
        'carga_horaria_total',
        'duracao_semanas',
        'status',
        'data_inicio',
        'data_fim',
        'instrutor_principal',
        'coordenador',
        'vagas_maximo',
        'certificado_obrigatorio',
        'frequencia_minima',
        'created_by',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'certificado_obrigatorio' => 'boolean',
        'carga_horaria_total' => 'integer',
        'duracao_semanas' => 'integer',
        'vagas_maximo' => 'integer',
        'frequencia_minima' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para tipos de curso
    public const TIPO_ESCOLA_DOMINICAL = 'escola_dominical';
    public const TIPO_PREPARACAO_BATISMO = 'preparacao_batismo';
    public const TIPO_CURSO_MEMBROS = 'curso_membros';
    public const TIPO_LIDERANCA = 'lideranca';
    public const TIPO_MINISTERIAL = 'ministerial';
    public const TIPO_CASAIS = 'casais';
    public const TIPO_JOVENS = 'jovens';
    public const TIPO_OUTRO = 'outro';

    public const TIPOS = [
        self::TIPO_ESCOLA_DOMINICAL,
        self::TIPO_PREPARACAO_BATISMO,
        self::TIPO_CURSO_MEMBROS,
        self::TIPO_LIDERANCA,
        self::TIPO_MINISTERIAL,
        self::TIPO_CASAIS,
        self::TIPO_JOVENS,
        self::TIPO_OUTRO,
    ];

    // Constantes para status
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

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function instrutorPrincipal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instrutor_principal');
    }

    public function coordenadorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordenador');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function turmas(): HasMany
    {
        return $this->hasMany(CursoTurma::class, 'curso_id');
    }

    public function pedidosEspeciais(): HasMany
    {
        return $this->hasMany(PedidoEspecial::class, 'curso_id');
    }

    // 🔗 RELACIONAMENTOS ESPECIAIS
    public function turmasAtivas(): HasMany
    {
        return $this->turmas()->where('status', self::STATUS_ATIVO);
    }

    public function turmasPlanejadas(): HasMany
    {
        return $this->turmas()->where('status', self::STATUS_PLANEJADO);
    }

    public function turmasConcluidas(): HasMany
    {
        return $this->turmas()->where('status', self::STATUS_CONCLUIDO);
    }

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isPlanejado(): bool
    {
        return $this->status === self::STATUS_PLANEJADO;
    }

    public function isAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function isConcluido(): bool
    {
        return $this->status === self::STATUS_CONCLUIDO;
    }

    public function isSuspenso(): bool
    {
        return $this->status === self::STATUS_SUSPENSO;
    }

    public function isCancelado(): bool
    {
        return $this->status === self::STATUS_CANCELADO;
    }

    public function isCertificadoObrigatorio(): bool
    {
        return $this->certificado_obrigatorio === true;
    }

    // ========================================
    // Scopes
    // ========================================
    public function scopeAtivos($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    public function scopePlanejados($query)
    {
        return $query->where('status', self::STATUS_PLANEJADO);
    }

    public function scopeConcluidos($query)
    {
        return $query->where('status', self::STATUS_CONCLUIDO);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    // ========================================
    // Accessors
    // ========================================
    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            self::TIPO_ESCOLA_DOMINICAL => 'Escola Dominical',
            self::TIPO_PREPARACAO_BATISMO => 'Preparação para Batismo',
            self::TIPO_CURSO_MEMBROS => 'Curso de Membros',
            self::TIPO_LIDERANCA => 'Liderança',
            self::TIPO_MINISTERIAL => 'Ministerial',
            self::TIPO_CASAIS => 'Casais',
            self::TIPO_JOVENS => 'Jovens',
            self::TIPO_OUTRO => 'Outro',
            default => 'Não definido',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PLANEJADO => 'Planejado',
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_CONCLUIDO => 'Concluído',
            self::STATUS_SUSPENSO => 'Suspenso',
            self::STATUS_CANCELADO => 'Cancelado',
            default => 'Não definido',
        };
    }

    public function getDuracaoFormatadaAttribute(): string
    {
        if ($this->duracao_semanas) {
            $semanas = $this->duracao_semanas;
            return $semanas === 1 ? '1 semana' : "{$semanas} semanas";
        }
        return 'Não definido';
    }

    public function getCargaHorariaFormatadaAttribute(): string
    {
        if ($this->carga_horaria_total) {
            $horas = $this->carga_horaria_total;
            return $horas === 1 ? '1 hora' : "{$horas} horas";
        }
        return 'Não definido';
    }
}
