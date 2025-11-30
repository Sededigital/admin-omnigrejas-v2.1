<?php

namespace App\Models\Igrejas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaAlianca extends Model
{
    use HasFactory;

    protected $table = 'igreja_aliancas';

    protected $fillable = [
        'igreja_id',
        'alianca_id',
        'status',
        'data_adesao',
        'data_desligamento',
        'observacoes',
        'created_by',
    ];

    protected $casts = [
        'data_adesao' => 'datetime',
        'data_desligamento' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Igrejas\Igreja::class, 'igreja_id');
    }

    public function alianca(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Igrejas\AliancaIgreja::class, 'alianca_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Relacionamento com líderes da aliança
    public function lideres()
    {
        return $this->hasMany(AliancaLider::class, 'igreja_alianca_id');
    }

    public function lideresAtivos()
    {
        return $this->lideres()->where('ativo', true);
    }

    // ========================================
    // HELPERS PARA STATUS
    // ========================================
    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function isInativo(): bool
    {
        return $this->status === 'inativo';
    }

    public function isSuspenso(): bool
    {
        return $this->status === 'suspenso';
    }

    // ========================================
    // MÉTODOS DE CONVENIÊNCIA
    // ========================================
    public function ativar(): void
    {
        $this->update(['status' => 'ativo']);
    }

    public function inativar(): void
    {
        $this->update(['status' => 'inativo']);
    }

    public function suspender(): void
    {
        $this->update(['status' => 'suspenso']);
    }

    public function desligar(string $motivo = null): void
    {
        $this->update([
            'status' => 'inativo',
            'data_desligamento' => now(),
            'observacoes' => $motivo ? $this->observacoes . "\n\nDesligamento: " . $motivo : $this->observacoes
        ]);
    }

    public function reativar(): void
    {
        $this->update([
            'status' => 'ativo',
            'data_desligamento' => null
        ]);
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeInativas($query)
    {
        return $query->where('status', 'inativo');
    }

    public function scopeSuspensas($query)
    {
        return $query->where('status', 'suspenso');
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopePorAlianca($query, $aliancaId)
    {
        return $query->where('alianca_id', $aliancaId);
    }
}
