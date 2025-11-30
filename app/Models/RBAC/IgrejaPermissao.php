<?php

namespace App\Models\RBAC;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaPermissao extends Model
{
    use HasFactory;

    protected $table = 'igreja_permissoes';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'igreja_id',
        'codigo',
        'nome',
        'descricao',
        'modulo',
        'categoria',
        'nivel_hierarquia',
        'ativo',
        'created_by',
    ];

    protected $casts = [
        'nivel_hierarquia' => 'integer',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function funcoes(): BelongsToMany
    {
        return $this->belongsToMany(
            IgrejaFuncao::class,
            'igreja_funcao_permissoes',
            'permissao_id',
            'funcao_id'
        )->withPivot(['concedido_em', 'concedido_por'])
         ->withTimestamps();
    }

    // ========================================
    // HELPERS PARA CHECAGEM RÁPIDA
    // ========================================
    public function isAtiva(): bool
    {
        return $this->ativo === true;
    }

    public function isInativa(): bool
    {
        return $this->ativo === false;
    }

    public function isAdmin(): bool
    {
        return $this->categoria === 'admin';
    }

    public function isVisualizacao(): bool
    {
        return $this->categoria === 'visualizacao';
    }

    public function isEdicao(): bool
    {
        return $this->categoria === 'edicao';
    }

    public function getNivelHierarquiaLabel(): string
    {
        return match($this->nivel_hierarquia) {
            1, 2, 3 => 'Básico',
            4, 5, 6 => 'Intermediário',
            7, 8, 9 => 'Avançado',
            10 => 'Administrativo',
            default => 'Desconhecido'
        };
    }

    public function getCategoriaLabel(): string
    {
        return match($this->categoria) {
            'admin' => 'Administração',
            'visualizacao' => 'Visualização',
            'edicao' => 'Edição',
            default => ucfirst($this->categoria ?? 'Desconhecido')
        };
    }

    // ========================================
    // MÉTODOS DE CONVERSÃO DE NÍVEL
    // ========================================
    public static function getNivelOptions(): array
    {
        return [
            1 => 'baixo',
            2 => 'baixo',
            3 => 'baixo',
            4 => 'medio',
            5 => 'medio',
            6 => 'medio',
            7 => 'alto',
            8 => 'alto',
            9 => 'alto',
            10 => 'critico'
        ];
    }

    public static function getNivelLabels(): array
    {
        return [
            1 => 'Baixo (1)',
            2 => 'Baixo (2)',
            3 => 'Baixo (3)',
            4 => 'Médio (4)',
            5 => 'Médio (5)',
            6 => 'Médio (6)',
            7 => 'Alto (7)',
            8 => 'Alto (8)',
            9 => 'Alto (9)',
            10 => 'Crítico (10)'
        ];
    }

    public static function convertStringToNumber(string $nivelString): int
    {
        $mapping = [
            'baixo' => 1,
            'medio' => 4,
            'alto' => 7,
            'critico' => 10
        ];

        return $mapping[$nivelString] ?? 1;
    }

    public static function convertNumberToString(int $nivelNumber): string
    {
        $options = self::getNivelOptions();
        return $options[$nivelNumber] ?? 'baixo';
    }

    public function getNivelString(): string
    {
        return self::convertNumberToString($this->nivel_hierarquia);
    }

    public function setNivelFromString(string $nivelString): void
    {
        $this->nivel_hierarquia = self::convertStringToNumber($nivelString);
    }

    public function getModuloLabel(): string
    {
        return match($this->modulo) {
            'membros' => 'Gestão de Membros',
            'financeiro' => 'Financeiro',
            'eventos' => 'Eventos e Escalas',
            'cursos' => 'Cursos e Educação',
            'social' => 'Social e Comunicação',
            'recursos' => 'Recursos e Voluntariado',
            'marketplace' => 'Marketplace',
            'relatorios' => 'Relatórios',
            'pedidos' => 'Pedidos Especiais',
            'engajamento' => 'Engajamento',
            'pastoral' => 'Atendimentos Pastorais',
            'doacoes' => 'Doações',
            'aliancas' => 'Alianças',
            default => ucfirst($this->modulo ?? 'Desconhecido')
        };
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativas($query)
    {
        return $query->where('ativo', false);
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel_hierarquia', $nivel);
    }

    public function scopePorNivelMinimo($query, $nivelMinimo)
    {
        return $query->where('nivel_hierarquia', '>=', $nivelMinimo);
    }

    public function scopePorNivelMaximo($query, $nivelMaximo)
    {
        return $query->where('nivel_hierarquia', '<=', $nivelMaximo);
    }

    // ========================================
    // MÉTODOS DE NEGÓCIO
    // ========================================
    public function ativar(): bool
    {
        $this->ativo = true;
        return $this->save();
    }

    public function desativar(): bool
    {
        $this->ativo = false;
        return $this->save();
    }

    public function temFuncoes(): bool
    {
        return $this->funcoes()->exists();
    }

    public function contarFuncoes(): int
    {
        return $this->funcoes()->count();
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================
    public static function permissoesPorModulo($igrejaId)
    {
        return static::porIgreja($igrejaId)
            ->ativas()
            ->orderBy('modulo')
            ->orderBy('nivel_hierarquia')
            ->get()
            ->groupBy('modulo');
    }

    public static function buscarPorCodigo($igrejaId, $codigo)
    {
        return static::porIgreja($igrejaId)
            ->where('codigo', $codigo)
            ->ativas()
            ->first();
    }

    // ========================================
    // BOOT METHOD
    // ========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permissao) {
            if (empty($permissao->id)) {
                $permissao->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
