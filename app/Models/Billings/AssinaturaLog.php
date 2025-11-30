<?php

namespace App\Models\Billings;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Billings\Pacote;
use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaLog extends Model
{
    protected $table = 'assinatura_logs';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'pacote_id',
        'acao',
        'descricao',
        'usuario_id',
        'data_acao',
        'detalhes',
    ];

    protected $casts = [
        'data_acao' => 'datetime',
        'detalhes'  => 'array',
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

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(AssinaturaHistorico::class, 'assinatura_id');
    }

    public function pagamento(): BelongsTo
    {
        return $this->belongsTo(AssinaturaPagamento::class, 'pagamento_id');
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function isAcao($acao): bool
    {
        return $this->acao === $acao;
    }

    public function isCriado(): bool
    {
        return $this->isAcao('criado');
    }

    public function isUpgrade(): bool
    {
        return $this->isAcao('upgrade');
    }

    public function isDowngrade(): bool
    {
        return $this->isAcao('downgrade');
    }

    public function isCancelado(): bool
    {
        return $this->isAcao('cancelado');
    }

    public function isRenovado(): bool
    {
        return $this->isAcao('renovado');
    }

    public function isPagamento(): bool
    {
        return $this->isAcao('pagamento');
    }

    public function isExpirado(): bool
    {
        return $this->isAcao('expirado');
    }

    public function getDataFormatada(): string
    {
        return $this->data_acao->format('d/m/Y H:i');
    }

    public function getDataRelativa(): string
    {
        return $this->data_acao->diffForHumans();
    }

    public function getDetalhesFormatados(): array
    {
        if (is_string($this->detalhes)) {
            return json_decode($this->detalhes, true) ?? [];
        }

        return $this->detalhes ?? [];
    }

    public function getValorDetalhes($chave, $padrao = null)
    {
        $detalhes = $this->getDetalhesFormatados();
        return $detalhes[$chave] ?? $padrao;
    }
}
