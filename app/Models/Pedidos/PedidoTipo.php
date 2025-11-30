<?php

namespace App\Models\Pedidos;

use App\Models\Igrejas\CategoriaIgreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoTipo extends Model
{
    use HasFactory;

    protected $table = 'pedido_tipos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false; // Sem timestamps no schema

    protected $fillable = [
        'id',
        'nome',
        'descricao',
        'categoria_id',
        'igreja_id',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // 🔗 RELACIONAMENTOS
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaIgreja::class, 'categoria_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Igrejas\Igreja::class, 'igreja_id');
    }

    public function pedidosEspeciais(): HasMany
    {
        return $this->hasMany(PedidoEspecial::class, 'pedido_tipo_id');
    }

    // 🔗 RELACIONAMENTOS ESPECIAIS
    public function pedidosEspeciaisAtivos(): HasMany
    {
        return $this->pedidosEspeciais()->whereIn('status', ['pendente', 'em_andamento', 'aprovado']);
    }

    public function pedidosEspeciaisPendentes(): HasMany
    {
        return $this->pedidosEspeciais()->where('status', 'pendente');
    }

    public function pedidosEspeciaisAprovados(): HasMany
    {
        return $this->pedidosEspeciais()->where('status', 'aprovado');
    }

    public function pedidosEspeciaisConcluidos(): HasMany
    {
        return $this->pedidosEspeciais()->where('status', 'concluido');
    }

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isAtivo(): bool
    {
        return $this->ativo === true;
    }

    public function temPedidosAtivos(): bool
    {
        return $this->pedidosEspeciaisAtivos()->exists();
    }

    // ========================================
    // Scopes
    // ========================================
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeComPedidos($query)
    {
        return $query->whereHas('pedidosEspeciais');
    }

    public function scopeSemPedidos($query)
    {
        return $query->whereDoesntHave('pedidosEspeciais');
    }

    // ========================================
    // Accessors
    // ========================================
    public function getStatusLabelAttribute(): string
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->ativo ? 'badge-success' : 'badge-secondary';
    }

    public function getNomeCategoriaAttribute(): string
    {
        return $this->categoria->nome ?? 'Categoria não definida';
    }

    public function getTotalPedidosAttribute(): int
    {
        return $this->pedidosEspeciais()->count();
    }

    public function getTotalPedidosPendentesAttribute(): int
    {
        return $this->pedidosEspeciaisPendentes()->count();
    }

    public function getTotalPedidosAprovadosAttribute(): int
    {
        return $this->pedidosEspeciaisAprovados()->count();
    }

    public function getTotalPedidosConcluidosAttribute(): int
    {
        return $this->pedidosEspeciaisConcluidos()->count();
    }

    // ========================================
    // Métodos de negócio
    // ========================================
    public function ativar(): bool
    {
        $this->ativo = true;
        return $this->save();
    }

    public function desativar(): bool
    {
        // Verificar se não há pedidos ativos antes de desativar
        if ($this->temPedidosAtivos()) {
            return false;
        }

        $this->ativo = false;
        return $this->save();
    }

    public function podeSerExcluido(): bool
    {
        return !$this->pedidosEspeciais()->exists();
    }

    // ========================================
    // Métodos estáticos
    // ========================================
    public static function ativosPorCategoria(int $categoriaId): Collection
    {
        return self::ativos()->porCategoria($categoriaId)->get();
    }

    public static function maisUtilizados(int $limite = 10): Collection
    {
        return self::withCount('pedidosEspeciais')
            ->orderBy('pedidos_especiais_count', 'desc')
            ->limit($limite)
            ->get();
    }

    // ========================================
    // Boot method
    // ========================================
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pedidoTipo) {
            if (!$pedidoTipo->podeSerExcluido()) {
                throw new \Exception('Não é possível excluir um tipo de pedido que possui pedidos especiais associados.');
            }
        });
    }
}
