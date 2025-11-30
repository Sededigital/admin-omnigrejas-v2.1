<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Igrejas\Igreja;

class CartaoConfig extends Model
{
    use HasFactory;

    protected $table = 'cartao_config';

    protected $fillable = [
        'igreja_id',
        'cor_fundo_header',
        'cor_texto_header',
        'cor_texto_principal',
        'cor_texto_secundario',
        'cor_acento',
        'cor_status_ativo',
        'cor_status_inativo',
        'cor_status_perdido',
        'cor_status_danificado',
        'cor_status_renovado',
        'cor_status_cancelado',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com a igreja
     */
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    /**
     * Relacionamento com o usuário que criou
     */
    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Buscar configuração da igreja
     */
    public static function getConfiguracaoIgreja($igrejaId)
    {
        return static::where('igreja_id', $igrejaId)->first();
    }

    /**
     * Retornar cores padrão se não houver configuração
     */
    public static function getCoresPadrao()
    {
        return [
            'cor_fundo_header' => '#8B5CF6',
            'cor_texto_header' => '#FFFFFF',
            'cor_texto_principal' => '#1F2937',
            'cor_texto_secundario' => '#6B7280',
            'cor_acento' => '#8B5CF6',
            'cor_status_ativo' => '#10B981',
            'cor_status_inativo' => '#DC3545',
            'cor_status_perdido' => '#FD7E14',
            'cor_status_danificado' => '#6F42C1',
            'cor_status_renovado' => '#20C997',
            'cor_status_cancelado' => '#6C757D',
        ];
    }

    /**
     * Retornar configuração completa (com fallback para padrão)
     */
    public function getConfiguracaoCompleta()
    {
        $coresPadrao = static::getCoresPadrao();

        return [
            'cor_fundo_header' => $this->cor_fundo_header ?? $coresPadrao['cor_fundo_header'],
            'cor_texto_header' => $this->cor_texto_header ?? $coresPadrao['cor_texto_header'],
            'cor_texto_principal' => $this->cor_texto_principal ?? $coresPadrao['cor_texto_principal'],
            'cor_texto_secundario' => $this->cor_texto_secundario ?? $coresPadrao['cor_texto_secundario'],
            'cor_acento' => $this->cor_acento ?? $coresPadrao['cor_acento'],
            'cor_status_ativo' => $this->cor_status_ativo ?? $coresPadrao['cor_status_ativo'],
            'cor_status_inativo' => $this->cor_status_inativo ?? $coresPadrao['cor_status_inativo'],
            'cor_status_perdido' => $this->cor_status_perdido ?? $coresPadrao['cor_status_perdido'],
            'cor_status_danificado' => $this->cor_status_danificado ?? $coresPadrao['cor_status_danificado'],
            'cor_status_renovado' => $this->cor_status_renovado ?? $coresPadrao['cor_status_renovado'],
            'cor_status_cancelado' => $this->cor_status_cancelado ?? $coresPadrao['cor_status_cancelado'],
        ];
    }
}
