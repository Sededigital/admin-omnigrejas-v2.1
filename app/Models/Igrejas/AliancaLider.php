<?php

namespace App\Models\Igrejas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AliancaLider extends Model
{
    use HasFactory;

    protected $table = 'alianca_lideres';

    protected $fillable = [
        'igreja_alianca_id',
        'membro_id',
        'cargo_na_alianca',
        'observacoes',
        'ativo',
        'data_adesao',
        'data_desligamento',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_adesao' => 'datetime',
        'data_desligamento' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igrejaAlianca(): BelongsTo
    {
        return $this->belongsTo(IgrejaAlianca::class, 'igreja_alianca_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    // Relacionamentos através da participação da igreja
    public function igreja()
    {
        return $this->hasOneThrough(
            Igreja::class,
            IgrejaAlianca::class,
            'id', // Foreign key on IgrejaAlianca table
            'id', // Foreign key on Igreja table
            'igreja_alianca_id', // Local key on AliancaLider table
            'igreja_id' // Local key on IgrejaAlianca table
        );
    }

    public function alianca()
    {
        return $this->hasOneThrough(
            AliancaIgreja::class,
            IgrejaAlianca::class,
            'id', // Foreign key on IgrejaAlianca table
            'id', // Foreign key on AliancaIgreja table
            'igreja_alianca_id', // Local key on AliancaLider table
            'alianca_id' // Local key on IgrejaAlianca table
        );
    }

    // Removidos: relacionamentos diretos antigos
    // Agora usa igreja_alianca_id para relacionamentos indiretos

    // 🔗 SCOPES
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorParticipacao($query, $igrejaAliancaId)
    {
        return $query->where('igreja_alianca_id', $igrejaAliancaId);
    }

    public function scopePorMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function isInativo(): bool
    {
        return !$this->ativo;
    }

    public function desligar(): void
    {
        $this->update([
            'ativo' => false,
            'data_desligamento' => now(),
        ]);
    }

    public function reativar(): void
    {
        $this->update([
            'ativo' => true,
            'data_desligamento' => null,
        ]);
    }

    public function getCargoFormatado(): string
    {
        return match($this->cargo_na_alianca) {
            'admin' => 'Administrador',
            'pastor' => 'Pastor',
            'ministro' => 'Ministro',
            'obreiro' => 'Obreiro',
            'diacono' => 'Diácono',
            default => ucfirst($this->cargo_na_alianca ?? 'Membro')
        };
    }

    public function getStatusFormatado(): string
    {
        return $this->isAtivo() ? 'Ativo' : 'Inativo';
    }
}
