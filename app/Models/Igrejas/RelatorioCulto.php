<?php

namespace App\Models\Igrejas;

use App\Models\User;
use App\Models\Eventos\Evento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RelatorioCulto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'relatorio_culto';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'evento_id',
        'culto_padrao_id',
        'created_by',
        'titulo',
        'conteudo',
        'numero_participantes',
        'valor_oferta',
        'observacoes',
        'status',
        'data_relatorio',
        // Novos campos para estatísticas detalhadas
        'numero_visitantes',
        'numero_decisoes',
        'numero_batismos',
        'numero_conversoes',
        'numero_reconciliacoes',
        'numero_casamentos',
        'numero_funeral',
        'numero_outros_eventos',
        // Novos campos para valores financeiros
        'valor_dizimos',
        'valor_ofertas',
        'valor_doacoes',
        'valor_outros',
        // Novos campos para informações do culto
        'tema_culto',
        'pregador',
        'pregador_convidado',
        'texto_base',
        'resumo_mensagem',
        'tipo_culto',
        'dirigente',
        'musica_responsavel',
        'observacoes_gerais',
        // Campos de avaliação
        'avaliado_por',
        'data_avaliacao',
    ];

    protected $casts = [
        'numero_participantes' => 'integer',
        'valor_oferta' => 'decimal:2',
        'data_relatorio' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        // Novos campos para estatísticas detalhadas
        'numero_visitantes' => 'integer',
        'numero_decisoes' => 'integer',
        'numero_batismos' => 'integer',
        'numero_conversoes' => 'integer',
        'numero_reconciliacoes' => 'integer',
        'numero_casamentos' => 'integer',
        'numero_funeral' => 'integer',
        'numero_outros_eventos' => 'integer',
        // Novos campos para valores financeiros
        'valor_dizimos' => 'decimal:2',
        'valor_ofertas' => 'decimal:2',
        'valor_doacoes' => 'decimal:2',
        'valor_outros' => 'decimal:2',
        // Campos de avaliação
        'data_avaliacao' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function cultoPadrao(): BelongsTo
    {
        return $this->belongsTo(CultoPadrao::class, 'culto_padrao_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🔗 SCOPES
    public function scopeRascunhos($query)
    {
        return $query->where('status', 'rascunho');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('status', 'finalizado');
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopePorData($query, $data)
    {
        return $query->where('data_relatorio', $data);
    }

    // 🔗 HELPERS
    public function isRascunho(): bool
    {
        return $this->status === 'rascunho';
    }

    public function isFinalizado(): bool
    {
        return $this->status === 'finalizado';
    }

    public function finalizar(): void
    {
        $this->update(['status' => 'finalizado']);
    }

    public function voltarParaRascunho(): void
    {
        $this->update(['status' => 'rascunho']);
    }

    public function getStatusFormatado(): string
    {
        return match($this->status) {
            'rascunho' => 'Rascunho',
            'finalizado' => 'Finalizado',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'rascunho' => 'warning',
            'finalizado' => 'success',
            default => 'secondary'
        };
    }
}
