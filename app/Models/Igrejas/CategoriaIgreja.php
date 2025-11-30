<?php

namespace App\Models\Igrejas;

use App\Models\Igrejas\Igreja;
use App\Models\Pedidos\PedidoTipo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaIgreja extends Model
{
    use HasFactory;

    protected $table = 'categorias_igrejas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nome',
        'descricao',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'created_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igrejas(): HasMany
    {
        return $this->hasMany(Igreja::class, 'categoria_id');
    }

    public function pedidoTipos(): HasMany
    {
        return $this->hasMany(PedidoTipo::class, 'categoria_id');
    }

    // 🔗 RELACIONAMENTOS ESPECIAIS
    public function igrejasAtivas(): HasMany
    {
        return $this->igrejas()->where('status_aprovacao', 'aprovado');
    }

    public function pedidoTiposAtivos(): HasMany
    {
        return $this->pedidoTipos()->where('ativo', true);
    }

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isAtiva(): bool
    {
        return $this->ativa === true;
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }
}
