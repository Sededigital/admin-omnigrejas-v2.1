<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PagamentoAssinaturaIgreja extends Model
{
    use HasFactory;

    protected $table = 'pagamento_assinatura_igreja';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'igreja_id',
        'pacote_id',
        'valor',
        'preco_vitalicio',
        'duracao_meses',
        'is_vitalicio',
        'pacote_nome',
        'metodo_pagamento',
        'referencia',
        'comprovativo_url',
        'comprovativo_nome',
        'comprovativo_tipo',
        'comprovativo_tamanho',
        'status',
        'data_pagamento',
        'data_confirmacao',
        'confirmado_por',
        'observacoes',
        'motivo_rejeicao',
        'created_by',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'comprovativo_tamanho' => 'integer',
        'data_pagamento' => 'datetime',
        'data_confirmacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function confirmadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmado_por');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🔗 SCOPES
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeConfirmados($query)
    {
        return $query->where('status', 'confirmado');
    }

    public function scopeRejeitados($query)
    {
        return $query->where('status', 'rejeitado');
    }

    public function scopeExpirados($query)
    {
        return $query->where('status', 'expirado');
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopePorPacote($query, $pacoteId)
    {
        return $query->where('pacote_id', $pacoteId);
    }

    public function scopePorMetodo($query, $metodo)
    {
        return $query->where('metodo_pagamento', $metodo);
    }

    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('data_pagamento', '>=', now()->subDays($dias));
    }

    public function scopeComComprovativo($query)
    {
        return $query->whereNotNull('comprovativo_url');
    }

    public function scopeSemComprovativo($query)
    {
        return $query->whereNull('comprovativo_url');
    }

    // 🔗 HELPERS
    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function isConfirmado(): bool
    {
        return $this->status === 'confirmado';
    }

    public function isRejeitado(): bool
    {
        return $this->status === 'rejeitado';
    }

    public function isExpirado(): bool
    {
        return $this->status === 'expirado';
    }

    public function temComprovativo(): bool
    {
        return !empty($this->comprovativo_url);
    }

    public function podeSerConfirmado(): bool
    {
        return $this->isPendente() && $this->temComprovativo();
    }

    public function podeSerRejeitado(): bool
    {
        return $this->isPendente();
    }

    public function getValorFormatado(): string
    {
        return 'Kz ' . number_format($this->valor, 2, ',', '.');
    }

    public function getMetodoFormatado(): string
    {
        return match($this->metodo_pagamento) {
            'deposito' => 'Depósito Bancário',
            'multicaixa_express' => 'Multicaixa Express',
            'tpa' => 'TPA',
            'transferencia' => 'Transferência',
            'outro' => 'Outro',
            default => ucfirst($this->metodo_pagamento)
        };
    }

    public function getStatusFormatado(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'confirmado' => 'Confirmado',
            'rejeitado' => 'Rejeitado',
            'expirado' => 'Expirado',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pendente' => 'warning',
            'confirmado' => 'success',
            'rejeitado' => 'danger',
            'expirado' => 'secondary',
            default => 'secondary'
        };
    }

    public function getDataPagamentoFormatada(): string
    {
        return $this->data_pagamento->format('d/m/Y H:i');
    }

    public function getDataConfirmacaoFormatada(): ?string
    {
        return $this->data_confirmacao ? $this->data_confirmacao->format('d/m/Y H:i') : null;
    }

    public function getComprovativoTamanhoFormatado(): string
    {
        if (!$this->comprovativo_tamanho) {
            return 'N/A';
        }

        $bytes = $this->comprovativo_tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getComprovativoIcone(): string
    {
        if (!$this->comprovativo_tipo) {
            return 'fas fa-file';
        }

        if (str_contains($this->comprovativo_tipo, 'pdf')) {
            return 'fas fa-file-pdf';
        }

        if (str_contains($this->comprovativo_tipo, 'image/')) {
            return 'fas fa-file-image';
        }

        return 'fas fa-file';
    }

    public function confirmar(User $usuario, string $observacoes = null): bool
    {
        if (!$this->podeSerConfirmado()) {
            return false;
        }

        $this->update([
            'status' => 'confirmado',
            'data_confirmacao' => now(),
            'confirmado_por' => $usuario->id,
            'observacoes' => $observacoes,
        ]);

        return true;
    }

    public function rejeitar(User $usuario, string $motivo): bool
    {
        if (!$this->podeSerRejeitado()) {
            return false;
        }

        $this->update([
            'status' => 'rejeitado',
            'confirmado_por' => $usuario->id,
            'observacoes' => $motivo,
            'motivo_rejeicao' => $motivo,
        ]);

        return true;
    }

    public function expirar(): void
    {
        $this->update(['status' => 'expirado']);
    }

    public function getDiasDesdePagamento(): int
    {
        return $this->data_pagamento->diffInDays(now());
    }

    public function getDiasDesdeConfirmacao(): ?int
    {
        return $this->data_confirmacao ? $this->data_confirmacao->diffInDays(now()) : null;
    }

    public function estaExpirando(): bool
    {
        // Considera expirando se tem mais de 30 dias sem confirmação
        return $this->isPendente() && $this->getDiasDesdePagamento() > 30;
    }

    // 🔗 MÉTODOS ESTÁTICOS PARA RELATÓRIOS
    public static function getTotalPorStatus($igrejaId = null, $dias = 30)
    {
        $query = self::where('data_pagamento', '>=', now()->subDays($dias));

        if ($igrejaId) {
            $query->where('igreja_id', $igrejaId);
        }

        return $query->selectRaw('status, COUNT(*) as total, SUM(valor) as valor_total')
                    ->groupBy('status')
                    ->get();
    }

    public static function getTotalPorMetodo($igrejaId = null, $dias = 30)
    {
        $query = self::where('data_pagamento', '>=', now()->subDays($dias));

        if ($igrejaId) {
            $query->where('igreja_id', $igrejaId);
        }

        return $query->selectRaw('metodo_pagamento, COUNT(*) as total, SUM(valor) as valor_total')
                    ->groupBy('metodo_pagamento')
                    ->get();
    }
}