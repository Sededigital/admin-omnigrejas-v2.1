<?php

namespace App\Models\Marketplace;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketplaceProduto extends Model
{
    use HasFactory;

    protected $table = 'marketplace_produtos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'nome',
        'descricao',
        'preco',
        'estoque',
        'ativo',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
        'ativo' => 'boolean',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(MarketplacePedido::class, 'produto_id');
    }
}
