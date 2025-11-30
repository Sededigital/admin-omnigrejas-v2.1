<?php

namespace App\Models\Financeiro;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinanceiroAuditoria extends Model
{
    use HasFactory;

    protected $table = 'financeiro_auditoria';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'movimento_id',
        'valor_anterior',
        'valor_novo',
        'detalhes',
        'alterado_por',
        'data_alteracao',
    ];

    protected $casts = [
        'valor_anterior' => 'decimal:2',
        'valor_novo' => 'decimal:2',
        'detalhes' => 'array',
        'data_alteracao' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function movimento(): BelongsTo
    {
        return $this->belongsTo(FinanceiroMovimento::class, 'movimento_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alterado_por');
    }
}
