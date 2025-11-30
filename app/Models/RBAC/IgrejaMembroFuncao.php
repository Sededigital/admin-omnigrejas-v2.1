<?php

namespace App\Models\RBAC;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaMembroFuncao extends Model
{
    use HasFactory;

    protected $table = 'igreja_membro_funcoes';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'membro_id',
        'funcao_id',
        'igreja_id',
        'atribuido_por',
        'atribuido_em',
        'valido_ate',
        'status',
        'motivo_atribuicao',
        'observacoes',
        'revogado_por',
        'revogado_em',
    ];

    protected $casts = [
        'atribuido_em' => 'datetime',
        'valido_ate' => 'date',
        'revogado_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================
    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    public function funcao(): BelongsTo
    {
        return $this->belongsTo(IgrejaFuncao::class, 'funcao_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function atribuidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atribuido_por');
    }

    // ========================================
    // HELPERS PARA CHECAGEM RÁPIDA
    // ========================================
    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function isSuspenso(): bool
    {
        return $this->status === 'suspenso';
    }

    public function isRevogado(): bool
    {
        return $this->status === 'revogado';
    }

    public function estaValido(): bool
    {
        return $this->isAtivo() &&
               ($this->valido_ate === null || $this->valido_ate->isFuture());
    }

    public function estaExpirado(): bool
    {
        return $this->valido_ate !== null && $this->valido_ate->isPast();
    }

    public function temValidade(): bool
    {
        return $this->valido_ate !== null;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'ativo' => 'Ativo',
            'suspenso' => 'Suspenso',
            'revogado' => 'Revogado',
            default => ucfirst($this->status ?? 'Desconhecido')
        };
    }

    public function getTempoRestante(): ?string
    {
        if (!$this->temValidade()) {
            return null;
        }

        if ($this->estaExpirado()) {
            return 'Expirado';
        }

        return $this->valido_ate->diffForHumans();
    }

    public function getDiasRestantes(): ?int
    {
        if (!$this->temValidade() || $this->estaExpirado()) {
            return null;
        }

        return now()->diffInDays($this->valido_ate);
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeSuspensos($query)
    {
        return $query->where('status', 'suspenso');
    }

    public function scopeRevogados($query)
    {
        return $query->where('status', 'revogado');
    }

    public function scopeValidos($query)
    {
        return $query->where('status', 'ativo')
            ->where(function($q) {
                $q->whereNull('valido_ate')
                  ->orWhere('valido_ate', '>', now());
            });
    }

    public function scopeExpirados($query)
    {
        return $query->where('status', 'ativo')
            ->whereNotNull('valido_ate')
            ->where('valido_ate', '<=', now());
    }

    public function scopePorMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    public function scopePorFuncao($query, $funcaoId)
    {
        return $query->where('funcao_id', $funcaoId);
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeAtribuidosPor($query, $userId)
    {
        return $query->where('atribuido_por', $userId);
    }

    public function scopeComValidade($query)
    {
        return $query->whereNotNull('valido_ate');
    }

    public function scopeSemValidade($query)
    {
        return $query->whereNull('valido_ate');
    }

    public function scopeExpirandoEm($query, $dias = 30)
    {
        return $query->where('status', 'ativo')
            ->whereNotNull('valido_ate')
            ->where('valido_ate', '<=', now()->addDays($dias))
            ->where('valido_ate', '>', now());
    }

    // ========================================
    // MÉTODOS DE NEGÓCIO
    // ========================================
    public function ativar(): bool
    {
        $this->status = 'ativo';
        return $this->save();
    }

    public function suspender(): bool
    {
        $this->status = 'suspenso';
        return $this->save();
    }

    public function revogar(): bool
    {
        $this->status = 'revogado';
        return $this->save();
    }

    public function renovar($novaValidade = null): bool
    {
        if ($novaValidade) {
            $this->valido_ate = $novaValidade;
        }

        if ($this->isSuspenso()) {
            $this->status = 'ativo';
        }

        return $this->save();
    }

    public function podeSerEditadoPor(User $user): bool
    {
        // Admin da igreja pode editar
        if ($user->role === 'admin' && $this->igreja_id === $user->getIgrejaId()) {
            return true;
        }

        // Super admin pode editar tudo
        if (in_array($user->role, ['root', 'super_admin'])) {
            return true;
        }

        // Quem atribuiu pode editar (se ainda estiver ativo)
        if ($this->atribuido_por === $user->id && $this->isAtivo()) {
            return true;
        }

        return false;
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================
    public static function funcoesAtivasDoMembro($membroId)
    {
        return static::porMembro($membroId)
            ->validos()
            ->with(['funcao', 'atribuidoPor'])
            ->orderBy('atribuido_em', 'desc')
            ->get();
    }

    public static function membrosDaFuncao($funcaoId)
    {
        return static::porFuncao($funcaoId)
            ->validos()
            ->with(['membro.user', 'atribuidoPor'])
            ->orderBy('atribuido_em', 'desc')
            ->get();
    }

    public static function verificarAtribuicao($membroId, $funcaoId): bool
    {
        return static::where('membro_id', $membroId)
            ->where('funcao_id', $funcaoId)
            ->where('status', 'ativo')
            ->exists();
    }

    public static function contarFuncoesAtivasPorMembro($membroId): int
    {
        return static::porMembro($membroId)->validos()->count();
    }

    public static function contarMembrosAtivosPorFuncao($funcaoId): int
    {
        return static::porFuncao($funcaoId)->validos()->count();
    }

    // ========================================
    // BOOT METHOD
    // ========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($membroFuncao) {
            if (empty($membroFuncao->id)) {
                $membroFuncao->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
