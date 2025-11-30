<?php

namespace App\Models\RBAC;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaFuncaoPermissao extends Model
{
    use HasFactory;

    protected $table = 'igreja_funcao_permissoes';

    protected $fillable = [
        'funcao_id',
        'permissao_id',
        'concedido_em',
        'concedido_por',
    ];

    protected $casts = [
        'concedido_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================
    public function funcao(): BelongsTo
    {
        return $this->belongsTo(IgrejaFuncao::class, 'funcao_id');
    }

    public function permissao(): BelongsTo
    {
        return $this->belongsTo(IgrejaPermissao::class, 'permissao_id');
    }

    public function concedidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concedido_por');
    }

    // ========================================
    // HELPERS
    // ========================================
    public function foiConcedidoRecentemente(): bool
    {
        return $this->concedido_em->diffInDays(now()) <= 7;
    }

    public function getTempoDesdeConcessao(): string
    {
        return $this->concedido_em->diffForHumans();
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopeDaFuncao($query, $funcaoId)
    {
        return $query->where('funcao_id', $funcaoId);
    }

    public function scopeDaPermissao($query, $permissaoId)
    {
        return $query->where('permissao_id', $permissaoId);
    }

    public function scopeConcedidasPor($query, $userId)
    {
        return $query->where('concedido_por', $userId);
    }

    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('concedido_em', '>=', now()->subDays($dias));
    }

    public function scopeAntigas($query, $dias = 30)
    {
        return $query->where('concedido_em', '<', now()->subDays($dias));
    }

    // ========================================
    // MÉTODOS DE NEGÓCIO
    // ========================================
    public function revogar(): bool
    {
        return $this->delete();
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================
    public static function verificarRelacionamento($funcaoId, $permissaoId): bool
    {
        return static::where('funcao_id', $funcaoId)
            ->where('permissao_id', $permissaoId)
            ->exists();
    }

    public static function contarPermissoesPorFuncao($funcaoId): int
    {
        return static::daFuncao($funcaoId)->count();
    }

    public static function contarFuncoesPorPermissao($permissaoId): int
    {
        return static::daPermissao($permissaoId)->count();
    }
}
