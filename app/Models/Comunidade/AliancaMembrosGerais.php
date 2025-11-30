<?php

namespace App\Models\Comunidade;

use App\Models\Igrejas\IgrejaAlianca;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AliancaMembrosGerais extends Model
{
    use HasFactory;

    protected $table = 'alianca_membros_gerais';

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

    // 🔗 SCOPES
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    public function scopePorAlianca($query, $aliancaId)
    {
        return $query->where('igreja_alianca_id', $aliancaId);
    }

    public function scopePorMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    public function scopePorCargo($query, $cargo)
    {
        return $query->where('cargo_na_alianca', $cargo);
    }

    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('data_adesao', '>=', now()->subDays($dias));
    }

    // 🔗 HELPERS
    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function isInativo(): bool
    {
        return !$this->ativo;
    }

    public function foiDesligado(): bool
    {
        return !is_null($this->data_desligamento);
    }

    public function getCargoFormatado(): string
    {
        return match($this->cargo_na_alianca) {
            'admin' => 'Administrador',
            'pastor' => 'Pastor',
            'ministro' => 'Ministro',
            'obreiro' => 'Obreiro',
            'diacono' => 'Diácono',
            'membro' => 'Membro',
            default => ucfirst($this->cargo_na_alianca)
        };
    }

    public function getStatusFormatado(): string
    {
        if ($this->foiDesligado()) {
            return 'Desligado';
        }

        return $this->isAtivo() ? 'Ativo' : 'Inativo';
    }

    public function getDataAdesaoFormatada(): string
    {
        return $this->data_adesao->format('d/m/Y');
    }

    public function getDataDesligamentoFormatada(): string
    {
        return $this->data_desligamento ? $this->data_desligamento->format('d/m/Y') : 'Não desligado';
    }

    public function getDiasNaAlianca(): int
    {
        $dataFim = $this->data_desligamento ?? now();
        return $this->data_adesao->diffInDays($dataFim);
    }

    public function podeEditar(): bool
    {
        // Lógica para verificar se o usuário atual pode editar
        // Implementar baseado no contexto da aplicação
        return true; // Temporário
    }

    public function ativar(): void
    {
        $this->update([
            'ativo' => true,
            'data_desligamento' => null,
        ]);
    }

    public function desativar(): void
    {
        $this->update([
            'ativo' => false,
            'data_desligamento' => now(),
        ]);
    }

    public function alterarCargo($novoCargo): void
    {
        $this->update(['cargo_na_alianca' => $novoCargo]);
    }
}