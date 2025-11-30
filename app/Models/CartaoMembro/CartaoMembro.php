<?php

namespace App\Models\CartaoMembro;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use App\Traits\HasAuditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartaoMembro extends Model
{
    use HasFactory, HasAuditoria, SoftDeletes;

    protected $table = 'cartao_membro';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'membro_id',
        'igreja_id',
        'numero_cartao',
        'data_emissao',
        'data_validade',
        'status',
        'solicitado_em',
        'solicitado_por',
        'aprovado_em',
        'aprovado_por',
        'impresso_em',
        'impresso_por',
        'entregue_em',
        'entregue_por',
        'foto_url',
        'assinatura_digital',
        'qr_code',
        'template_usado',
        'custo_producao',
        'custo_entrega',
        'observacoes',
        'motivo_inativacao',
        'numero_renovacao',
        'created_by',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_validade' => 'date',
        'solicitado_em' => 'datetime',
        'aprovado_em' => 'datetime',
        'impresso_em' => 'datetime',
        'entregue_em' => 'datetime',
        'custo_producao' => 'decimal:2',
        'custo_entrega' => 'decimal:2',
        'numero_renovacao' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ========================================
    // CONSTANTES DE STATUS
    // ========================================
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INATIVO = 'inativo';
    public const STATUS_PERDIDO = 'perdido';
    public const STATUS_DANIFICADO = 'danificado';
    public const STATUS_RENOVADO = 'renovado';
    public const STATUS_CANCELADO = 'cancelado';

    public const STATUS_ARRAY = [
        self::STATUS_ATIVO,
        self::STATUS_INATIVO,
        self::STATUS_PERDIDO,
        self::STATUS_DANIFICADO,
        self::STATUS_RENOVADO,
        self::STATUS_CANCELADO,
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================
    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function solicitadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    public function impressoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'impresso_por');
    }

    public function entreguePor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregue_por');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(CartaoMembroHistorico::class, 'cartao_id');
    }

    // ========================================
    // HELPERS E MÉTODOS ÚTEIS
    // ========================================
    public function isAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function isInativo(): bool
    {
        return $this->status === self::STATUS_INATIVO;
    }

    public function isExpirado(): bool
    {
        return $this->data_validade && $this->data_validade->isPast();
    }

    public function precisaRenovar(): bool
    {
        if (!$this->data_validade) return false;

        // Usar 30 dias como padrão para antecedência de renovação
        $diasAntecedencia = 30;

        return $this->data_validade->diffInDays(now(), false) <= $diasAntecedencia;
    }

    public function getStatusFormatado(): string
    {
        return match($this->status) {
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_INATIVO => 'Inativo',
            self::STATUS_PERDIDO => 'Perdido',
            self::STATUS_DANIFICADO => 'Danificado',
            self::STATUS_RENOVADO => 'Renovado',
            self::STATUS_CANCELADO => 'Cancelado',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_ATIVO => 'success',
            self::STATUS_INATIVO => 'secondary',
            self::STATUS_PERDIDO => 'danger',
            self::STATUS_DANIFICADO => 'warning',
            self::STATUS_RENOVADO => 'info',
            self::STATUS_CANCELADO => 'dark',
            default => 'secondary'
        };
    }

    public function getDiasParaExpirar(): ?int
    {
        return $this->data_validade ? $this->data_validade->diffInDays(now(), false) : null;
    }

    public function getCustoTotal(): float
    {
        return ($this->custo_producao ?? 0) + ($this->custo_entrega ?? 0);
    }

    public function podeSerEditado(): bool
    {
        // Só pode editar se não foi entregue ou impresso
        return !$this->entregue_em && !$this->impresso_em;
    }

    public function podeSerCancelado(): bool
    {
        // Não pode cancelar se já foi entregue
        return !$this->entregue_em;
    }

    // ========================================
    // MÉTODOS DE AÇÃO
    // ========================================
    public function aprovar(User $usuario): bool
    {
        if ($this->aprovado_em) return false;

        $this->update([
            'status' => self::STATUS_ATIVO,
            'aprovado_em' => now(),
            'aprovado_por' => $usuario->id,
        ]);

        return true;
    }

    public function marcarComoImpresso(User $usuario): bool
    {
        if ($this->impresso_em) return false;

        $this->update([
            'impresso_em' => now(),
            'impresso_por' => $usuario->id,
        ]);

        return true;
    }

    public function marcarComoEntregue(User $usuario): bool
    {
        if ($this->entregue_em) return false;

        $this->update([
            'entregue_em' => now(),
            'entregue_por' => $usuario->id,
        ]);

        return true;
    }

    public function renovar(User $usuario): bool
    {
        // Usar 12 meses como padrão para validade
        $validadeMeses = 12;

        $novaValidade = now()->addMonths($validadeMeses);

        $this->update([
            'data_validade' => $novaValidade,
            'numero_renovacao' => ($this->numero_renovacao ?? 0) + 1,
            'status' => self::STATUS_ATIVO,
        ]);

        return true;
    }

    public function cancelar(string $motivo, User $usuario): bool
    {
        if (!$this->podeSerCancelado()) return false;

        $this->update([
            'status' => self::STATUS_CANCELADO,
            'motivo_inativacao' => $motivo,
        ]);

        return true;
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopeAtivos($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    public function scopeExpirados($query)
    {
        return $query->where('data_validade', '<', now());
    }

    public function scopePrecisamRenovar($query)
    {
        return $query->whereRaw("data_validade <= (CURRENT_DATE + INTERVAL '30 days')");
    }

    public function scopeDaIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeDoMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSolicitados($query)
    {
        return $query->whereNotNull('solicitado_em');
    }

    public function scopeAprovados($query)
    {
        return $query->whereNotNull('aprovado_em');
    }

    public function scopeImpressos($query)
    {
        return $query->whereNotNull('impresso_em');
    }

    public function scopeEntregues($query)
    {
        return $query->whereNotNull('entregue_em');
    }

}
