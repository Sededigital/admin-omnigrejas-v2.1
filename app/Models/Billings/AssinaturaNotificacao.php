<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaHistorico;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AssinaturaNotificacao extends Model
{
    protected $table = 'assinatura_notificacoes';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'assinatura_id',
        'tipo',
        'titulo',
        'mensagem',
        'enviada_em',
        'lida_em',
        'status',
    ];

    protected $casts = [
        'enviada_em' => 'datetime',
        'lida_em'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(AssinaturaHistorico::class, 'assinatura_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function isLembrete(): bool
    {
        return $this->tipo === 'lembrete';
    }

    public function isAtraso(): bool
    {
        return $this->tipo === 'atraso';
    }

    public function isCancelamento(): bool
    {
        return $this->tipo === 'cancelamento';
    }

    public function isEnviada(): bool
    {
        return $this->status === 'enviada';
    }

    public function isLida(): bool
    {
        return $this->status === 'lida';
    }

    public function isIgnorada(): bool
    {
        return $this->status === 'ignorada';
    }

    public function marcarComoLida(): void
    {
        $this->update([
            'status' => 'lida',
            'lida_em' => now()
        ]);
    }

    public function marcarComoIgnorada(): void
    {
        $this->update(['status' => 'ignorada']);
    }

    public function getTipoFormatado(): string
    {
        return match($this->tipo) {
            'lembrete' => 'Lembrete',
            'atraso' => 'Atraso',
            'cancelamento' => 'Cancelamento',
            default => ucfirst($this->tipo)
        };
    }

    public function getStatusFormatado(): string
    {
        return match($this->status) {
            'enviada' => 'Enviada',
            'lida' => 'Lida',
            'ignorada' => 'Ignorada',
            default => ucfirst($this->status)
        };
    }

    public function getDataEnvioFormatada(): string
    {
        return $this->enviada_em ? $this->enviada_em->format('d/m/Y H:i') : 'Não enviada';
    }

    public function getDataLeituraFormatada(): string
    {
        return $this->lida_em ? $this->lida_em->format('d/m/Y H:i') : 'Não lida';
    }

    public function foiLida(): bool
    {
        return !is_null($this->lida_em);
    }

    public function foiEnviada(): bool
    {
        return !is_null($this->enviada_em);
    }
}
