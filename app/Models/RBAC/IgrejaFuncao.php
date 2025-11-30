<?php

namespace App\Models\RBAC;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaFuncao extends Model
{
    use HasFactory;

    protected $table = 'igreja_funcoes';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'igreja_id',
        'nome',
        'descricao',
        'nivel_hierarquia',
        'cor_identificacao',
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

    public function permissoes(): BelongsToMany
    {
        return $this->belongsToMany(
            IgrejaPermissao::class,
            'igreja_funcao_permissoes',
            'funcao_id',
            'permissao_id'
        )->withPivot(['concedido_em', 'concedido_por'])
         ->withTimestamps();
    }

    public function membroFuncoes(): HasMany
    {
        return $this->hasMany(IgrejaMembroFuncao::class, 'funcao_id');
    }

    public function membrosAtivos(): HasMany
    {
        return $this->membroFuncoes()->where('status', 'ativo');
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

    public function temMembros(): bool
    {
        return $this->membroFuncoes()->where('status', 'ativo')->exists();
    }

    public function contarMembrosAtivos(): int
    {
        return $this->membroFuncoes()->where('status', 'ativo')->count();
    }

    public function temPermissoes(): bool
    {
        return $this->permissoes()->exists();
    }

    public function contarPermissoes(): int
    {
        return $this->permissoes()->count();
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

    public function getCorIdentificacaoPadrao(): string
    {
        return $this->cor_identificacao ?? '#6B7280';
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

    public function scopeComMembros($query)
    {
        return $query->whereHas('membroFuncoes', function($q) {
            $q->where('status', 'ativo');
        });
    }

    public function scopeSemMembros($query)
    {
        return $query->whereDoesntHave('membroFuncoes', function($q) {
            $q->where('status', 'ativo');
        });
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
        // Verificar se não há membros ativos antes de desativar
        if ($this->temMembros()) {
            return false;
        }

        $this->ativo = false;
        return $this->save();
    }

    public function podeSerExcluida(): bool
    {
        return !$this->temMembros();
    }

    public function adicionarPermissao(IgrejaPermissao $permissao, User $concedidoPor): bool
    {
        if ($this->permissoes()->where('permissao_id', $permissao->id)->exists()) {
            return true; // Já tem a permissão
        }

        $this->permissoes()->attach($permissao->id, [
            'concedido_por' => $concedidoPor->id,
            'concedido_em' => now(),
        ]);

        return true;
    }

    public function removerPermissao(IgrejaPermissao $permissao): bool
    {
        $this->permissoes()->detach($permissao->id);
        return true;
    }

    public function temPermissao($codigoPermissao): bool
    {
        return $this->permissoes()->where('codigo', $codigoPermissao)->exists();
    }

    public function getPermissoesPorModulo()
    {
        return $this->permissoes()
            ->orderBy('modulo')
            ->orderBy('nivel_hierarquia')
            ->get()
            ->groupBy('modulo');
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================
    public static function funcoesPorIgreja($igrejaId)
    {
        return static::porIgreja($igrejaId)
            ->ativas()
            ->withCount('membroFuncoes')
            ->orderBy('nivel_hierarquia')
            ->orderBy('nome')
            ->get();
    }

    public static function buscarPorNome($igrejaId, $nome)
    {
        return static::porIgreja($igrejaId)
            ->where('nome', $nome)
            ->ativas()
            ->first();
    }

    // ========================================
    // BOOT METHOD
    // ========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($funcao) {
            if (empty($funcao->id)) {
                $funcao->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
