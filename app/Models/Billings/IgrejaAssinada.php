<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\Billings\Pacote;
use App\Models\Billings\AssinaturaLog;
use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaCiclo;
use App\Models\Billings\AssinaturaCupomUso;
use App\Models\Billings\AssinaturaPagamento;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IgrejaAssinada extends Model
{
    protected $table = 'igrejas_assinadas';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'pacote_id',
        'ativo',
        'data_adesao',
        'data_cancelamento',
        'observacoes',
    ];

    protected $casts = [
        'ativo'            => 'boolean',
        'data_adesao'      => 'datetime',
        'data_cancelamento'=> 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssinaturaLog::class, 'igreja_id', 'igreja_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(AssinaturaPagamento::class, 'igreja_id', 'igreja_id');
    }

    public function ciclos(): HasMany
    {
        return $this->hasMany(AssinaturaCiclo::class, 'igreja_id', 'igreja_id');
    }

    public function cupomUsos(): HasMany
    {
        return $this->hasMany(AssinaturaCupomUso::class, 'igreja_id', 'igreja_id');
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

    public function isCancelado(): bool
    {
        return !is_null($this->data_cancelamento);
    }

    public function ativar(): void
    {
        $this->update([
            'ativo' => true,
            'data_cancelamento' => null
        ]);
    }

    public function cancelar(string $observacao = null): void
    {
        $this->update([
            'ativo' => false,
            'data_cancelamento' => now(),
            'observacoes' => $observacao
        ]);
    }

    public function getDataAdesaoFormatada(): string
    {
        return $this->data_adesao->format('d/m/Y H:i');
    }

    public function getDataCancelamentoFormatada(): string
    {
        return $this->data_cancelamento ? $this->data_cancelamento->format('d/m/Y H:i') : 'N/A';
    }

    public function getDataAdesaoRelativa(): string
    {
        return $this->data_adesao->diffForHumans();
    }

    public function getDataCancelamentoRelativa(): string
    {
        return $this->data_cancelamento ? $this->data_cancelamento->diffForHumans() : 'N/A';
    }

    public function getDiasDesdeAdesao(): int
    {
        return $this->data_adesao->diffInDays(now());
    }

    public function getDiasDesdeCancelamento(): int
    {
        if (!$this->data_cancelamento) {
            return 0;
        }

        return $this->data_cancelamento->diffInDays(now());
    }

    public function getStatusFormatado(): string
    {
        if ($this->isCancelado()) {
            return 'Cancelado';
        }

        return $this->isAtivo() ? 'Ativo' : 'Inativo';
    }

    public function getStatusClass(): string
    {
        if ($this->isCancelado()) {
            return 'danger';
        }

        return $this->isAtivo() ? 'success' : 'warning';
    }

    public function getObservacoesFormatadas(): string
    {
        return $this->observacoes ?: 'Sem observações';
    }

    public function getDuracaoAssinatura(): int
    {
        if ($this->isCancelado()) {
            return $this->data_adesao->diffInDays($this->data_cancelamento);
        }

        return $this->data_adesao->diffInDays(now());
    }

    public function getDuracaoAssinaturaFormatada(): string
    {
        $dias = $this->getDuracaoAssinatura();

        if ($dias < 30) {
            return $dias . ' dias';
        }

        $meses = floor($dias / 30);
        $diasRestantes = $dias % 30;

        if ($meses < 12) {
            return $meses . ' meses' . ($diasRestantes > 0 ? ' e ' . $diasRestantes . ' dias' : '');
        }

        $anos = floor($meses / 12);
        $mesesRestantes = $meses % 12;

        return $anos . ' anos' . ($mesesRestantes > 0 ? ' e ' . $mesesRestantes . ' meses' : '');
    }
}
