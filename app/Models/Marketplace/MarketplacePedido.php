<?php

namespace App\Models\Marketplace;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketplacePedido extends Model
{
    use HasFactory;

    protected $table = 'marketplace_pedidos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'produto_id',
        'igreja_id',
        'comprador_id',
        'quantidade',
        'status',
        'data_pedido',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'data_pedido' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function produto(): BelongsTo
    {
        return $this->belongsTo(MarketplaceProduto::class, 'produto_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function comprador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(MarketplacePagamento::class, 'pedido_id');
    }
}
