<?php

namespace App\Models\CartaoMembro;

use App\Models\User;
use App\Traits\HasAuditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartaoMembroHistorico extends Model
{
    use HasFactory, HasAuditoria;

    protected $table = 'cartao_membro_historico';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'cartao_id',
        'acao',
        'descricao',
        'realizado_por',
        'data_acao',
    ];

    protected $casts = [
        'data_acao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // CONSTANTES DE AÇÕES
    // ========================================
    public const ACAO_SOLICITADO = 'solicitado';
    public const ACAO_APROVADO = 'aprovado';
    public const ACAO_IMPRESSO = 'impresso';
    public const ACAO_ENTREGUE = 'entregue';
    public const ACAO_RENOVADO = 'renovado';
    public const ACAO_CANCELADO = 'cancelado';
    public const ACAO_PERDIDO = 'perdido';
    public const ACAO_DANIFICADO = 'danificado';

    public const ACOES_ARRAY = [
        self::ACAO_SOLICITADO,
        self::ACAO_APROVADO,
        self::ACAO_IMPRESSO,
        self::ACAO_ENTREGUE,
        self::ACAO_RENOVADO,
        self::ACAO_CANCELADO,
        self::ACAO_PERDIDO,
        self::ACAO_DANIFICADO,
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================
    public function cartao(): BelongsTo
    {
        return $this->belongsTo(CartaoMembro::class, 'cartao_id');
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }

    // ========================================
    // HELPERS E MÉTODOS ÚTEIS
    // ========================================
    public function getAcaoFormatada(): string
    {
        return match($this->acao) {
            self::ACAO_SOLICITADO => 'Solicitado',
            self::ACAO_APROVADO => 'Aprovado',
            self::ACAO_IMPRESSO => 'Impresso',
            self::ACAO_ENTREGUE => 'Entregue',
            self::ACAO_RENOVADO => 'Renovado',
            self::ACAO_CANCELADO => 'Cancelado',
            self::ACAO_PERDIDO => 'Reportado como Perdido',
            self::ACAO_DANIFICADO => 'Reportado como Danificado',
            default => ucfirst($this->acao)
        };
    }

    public function getAcaoIcone(): string
    {
        return match($this->acao) {
            self::ACAO_SOLICITADO => 'fas fa-plus-circle',
            self::ACAO_APROVADO => 'fas fa-check-circle',
            self::ACAO_IMPRESSO => 'fas fa-print',
            self::ACAO_ENTREGUE => 'fas fa-hand-holding',
            self::ACAO_RENOVADO => 'fas fa-sync-alt',
            self::ACAO_CANCELADO => 'fas fa-times-circle',
            self::ACAO_PERDIDO => 'fas fa-exclamation-triangle',
            self::ACAO_DANIFICADO => 'fas fa-tools',
            default => 'fas fa-info-circle'
        };
    }

    public function getAcaoBadgeClass(): string
    {
        return match($this->acao) {
            self::ACAO_SOLICITADO => 'primary',
            self::ACAO_APROVADO => 'success',
            self::ACAO_IMPRESSO => 'info',
            self::ACAO_ENTREGUE => 'success',
            self::ACAO_RENOVADO => 'warning',
            self::ACAO_CANCELADO => 'danger',
            self::ACAO_PERDIDO => 'danger',
            self::ACAO_DANIFICADO => 'warning',
            default => 'secondary'
        };
    }

    public function foiRealizadoRecentemente(): bool
    {
        return $this->data_acao->diffInMinutes(now()) < 5;
    }

    public function getTempoDecorrido(): string
    {
        return $this->data_acao->diffForHumans();
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopeDoCartao($query, $cartaoId)
    {
        return $query->where('cartao_id', $cartaoId);
    }

    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    public function scopeRealizadoPor($query, $usuarioId)
    {
        return $query->where('realizado_por', $usuarioId);
    }

    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('data_acao', '>=', now()->subDays($dias));
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('data_acao', today());
    }

    public function scopeEstaSemana($query)
    {
        return $query->whereBetween('data_acao', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeEsteMes($query)
    {
        return $query->whereYear('data_acao', now()->year)
                    ->whereMonth('data_acao', now()->month);
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================
    public static function registrarAcao(CartaoMembro $cartao, string $acao, string $descricao = null, User $usuario = null): self
    {
        return static::create([
            'cartao_id' => $cartao->id,
            'acao' => $acao,
            'descricao' => $descricao ?? self::getDescricaoPadrao($acao),
            'realizado_por' => $usuario ? $usuario->id : null,
            'data_acao' => now(),
        ]);
    }

    private static function getDescricaoPadrao(string $acao): string
    {
        return match($acao) {
            self::ACAO_SOLICITADO => 'Cartão solicitado para o membro',
            self::ACAO_APROVADO => 'Cartão aprovado para produção',
            self::ACAO_IMPRESSO => 'Cartão enviado para impressão',
            self::ACAO_ENTREGUE => 'Cartão entregue ao membro',
            self::ACAO_RENOVADO => 'Cartão renovado com nova validade',
            self::ACAO_CANCELADO => 'Cartão cancelado',
            self::ACAO_PERDIDO => 'Cartão reportado como perdido',
            self::ACAO_DANIFICADO => 'Cartão reportado como danificado',
            default => 'Ação realizada no cartão'
        };
    }
}
