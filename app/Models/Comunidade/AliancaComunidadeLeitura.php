<?php

namespace App\Models\Comunidade;

use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AliancaComunidadeLeitura extends Model
{
    use HasFactory;

    protected $table = 'alianca_comunidade_leituras';

    protected $fillable = [
        'mensagem_id',
        'membro_id',
        'lida_em',
    ];

    protected $casts = [
        'lida_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function mensagem(): BelongsTo
    {
        return $this->belongsTo(AliancaComunidadeMensagem::class, 'mensagem_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    // 🔗 HELPERS
    public function foiLidaRecentemente(): bool
    {
        return $this->lida_em->diffInMinutes(now()) < 5;
    }

    public function getTempoDesdeLeitura(): string
    {
        return $this->lida_em->diffForHumans();
    }

    public function getLidaEmFormatado(): string
    {
        return $this->lida_em->format('d/m/Y H:i:s');
    }

    // 🔗 SCOPES
    public function scopeDoMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    public function scopeDaMensagem($query, $mensagemId)
    {
        return $query->where('mensagem_id', $mensagemId);
    }

    public function scopeRecentes($query, $minutos = 60)
    {
        return $query->where('lida_em', '>=', now()->subMinutes($minutos));
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('lida_em', today());
    }

    public function scopeEstaSemana($query)
    {
        return $query->whereBetween('lida_em', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeEsteMes($query)
    {
        return $query->whereMonth('lida_em', now()->month)
                    ->whereYear('lida_em', now()->year);
    }
}