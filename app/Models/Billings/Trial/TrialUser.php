<?php

namespace App\Models\Billings\Trial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Igrejas\Igreja;

class TrialUser extends Model
{
    use HasFactory;

    protected $table = 'trial_users';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'igreja_id',
        'data_inicio',
        'data_fim',
        'periodo_dias',
        'status',
        'motivo_cancelamento',
        'pode_reativar',
        'reativado_em',
        'reativado_por',
        'periodo_graca_dias',
        'data_limite_graca',
        'total_membros_criados',
        'total_posts_criados',
        'total_eventos_criados',
        'ultimo_acesso',
        'criado_por',
        // Campos para manter dados após deleção do usuário
        'user_nome_deletado',
        'user_email_deletado',
        'user_telefone_deletado',
        'deletado_em'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'data_limite_graca' => 'date',
        'reativado_em' => 'datetime',
        'ultimo_acesso' => 'datetime',
        'deletado_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(TrialAlerta::class, 'trial_user_id');
    }

    public function dadosCriados(): HasMany
    {
        return $this->hasMany(TrialDadosCriados::class, 'trial_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TrialLog::class, 'trial_user_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function reativadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reativado_por');
    }

    // 🔗 SCOPES
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo')
                    ->where('data_fim', '>=', now());
    }

    public function scopeExpirados($query)
    {
        return $query->where('status', 'expirado');
    }

    public function scopeEmPeriodoGraca($query)
    {
        return $query->where('status', 'expirado')
                    ->where('data_limite_graca', '>=', now());
    }

    public function scopeBloqueados($query)
    {
        return $query->where('status', 'bloqueado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('status', 'cancelado');
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeExpirandoEmDias($query, $dias)
    {
        return $query->where('status', 'ativo')
                    ->where('data_fim', now()->addDays($dias));
    }

    // 🔗 HELPERS
    public function isAtivo(): bool
    {
        return $this->status === 'ativo' && !$this->estaExpirado();
    }

    public function estaExpirado(): bool
    {
        return now()->isAfter($this->data_fim);
    }

    public function estaEmPeriodoGraca(): bool
    {
        return $this->estaExpirado() && now()->isBefore($this->data_limite_graca);
    }

    public function podeSerReativado(): bool
    {
        return $this->pode_reativar && $this->estaEmPeriodoGraca();
    }

    public function foiReativado(): bool
    {
        return !is_null($this->reativado_em);
    }

    public function diasRestantes(): int
    {
        if ($this->estaExpirado()) return 0;
        return max(0, now()->diffInDays($this->data_fim, false));
    }

    public function diasEmGraca(): int
    {
        if (!$this->estaEmPeriodoGraca()) return 0;
        return max(0, now()->diffInDays($this->data_limite_graca, false));
    }

    public function diasDesdeCriacao(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function diasDesdeUltimoAcesso(): ?int
    {
        return $this->ultimo_acesso ? $this->ultimo_acesso->diffInDays(now()) : null;
    }

    public function getStatusFormatado(): string
    {
        return match($this->status) {
            'ativo' => 'Ativo',
            'expirando' => 'Expirando',
            'expirado' => 'Expirado',
            'bloqueado' => 'Bloqueado',
            'cancelado' => 'Cancelado',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'ativo' => 'success',
            'expirando' => 'warning',
            'expirado' => 'danger',
            'bloqueado' => 'dark',
            'cancelado' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusIcone(): string
    {
        return match($this->status) {
            'ativo' => 'fas fa-check-circle',
            'expirando' => 'fas fa-exclamation-triangle',
            'expirado' => 'fas fa-times-circle',
            'bloqueado' => 'fas fa-ban',
            'cancelado' => 'fas fa-times',
            default => 'fas fa-question-circle'
        };
    }

    public function getDataInicioFormatada(): string
    {
        return $this->data_inicio->format('d/m/Y');
    }

    public function getDataFimFormatada(): string
    {
        return $this->data_fim->format('d/m/Y');
    }

    public function getDataLimiteGracaFormatada(): string
    {
        return $this->data_limite_graca ? $this->data_limite_graca->format('d/m/Y') : 'N/A';
    }

    public function getReativadoEmFormatado(): string
    {
        return $this->reativado_em ? $this->reativado_em->format('d/m/Y H:i') : 'Nunca';
    }

    public function getUltimoAcessoFormatado(): string
    {
        return $this->ultimo_acesso ? $this->ultimo_acesso->format('d/m/Y H:i') : 'Nunca';
    }

    public function getEstatisticasUso(): array
    {
        return [
            'membros_criados' => $this->total_membros_criados,
            'posts_criados' => $this->total_posts_criados,
            'eventos_criados' => $this->total_eventos_criados,
            'total_itens' => $this->total_membros_criados + $this->total_posts_criados + $this->total_eventos_criados,
        ];
    }

    public function atualizarUltimoAcesso(): void
    {
        $this->update(['ultimo_acesso' => now()]);
    }

    public function incrementarMembrosCriados(): void
    {
        $this->increment('total_membros_criados');
    }

    public function incrementarPostsCriados(): void
    {
        $this->increment('total_posts_criados');
    }

    public function incrementarEventosCriados(): void
    {
        $this->increment('total_eventos_criados');
    }

    public function expirar(): void
    {
        $this->update(['status' => 'expirado']);
    }

    public function bloquear(string $motivo = null): void
    {
        $this->update([
            'status' => 'bloqueado',
            'motivo_cancelamento' => $motivo,
        ]);
    }

    public function cancelar(string $motivo = null): void
    {
        $this->update([
            'status' => 'cancelado',
            'motivo_cancelamento' => $motivo,
        ]);
    }

    public function reativar(User $admin, int $diasExtensao = 7): bool
    {
        if (!$this->podeSerReativado()) {
            return false;
        }

        $this->update([
            'status' => 'ativo',
            'reativado_em' => now(),
            'reativado_por' => $admin->id,
            'data_fim' => now()->addDays($diasExtensao),
            'data_limite_graca' => now()->addDays($diasExtensao + $this->periodo_graca_dias),
        ]);

        return true;
    }

    public function deveSerNotificadoExpiracao(): bool
    {
        return $this->isAtivo() && in_array($this->diasRestantes(), [7, 1]);
    }

    public function deveSerExpirado(): bool
    {
        return $this->isAtivo() && $this->estaExpirado();
    }

    public function deveSerLimpo(): bool
    {
        return $this->status === 'expirado' &&
                $this->data_limite_graca &&
                now()->isAfter($this->data_limite_graca->addDays(30));
    }

    /**
     * Verifica se o usuário foi deletado (dados mantidos apenas no trial)
     */
    public function usuarioFoiDeletado(): bool
    {
        return !is_null($this->deletado_em);
    }

    /**
     * Retorna informações do usuário (se deletado, usa dados armazenados)
     */
    public function getUserInfo(): array
    {
        if ($this->usuarioFoiDeletado()) {
            return [
                'nome' => $this->user_nome_deletado ?? 'Usuário Deletado',
                'email' => $this->user_email_deletado ?? 'N/A',
                'telefone' => $this->user_telefone_deletado ?? 'N/A',
                'deletado_em' => $this->deletado_em,
                'status' => 'deletado'
            ];
        }

        if ($this->user) {
            return [
                'nome' => $this->user->name,
                'email' => $this->user->email,
                'telefone' => $this->user->phone,
                'deletado_em' => null,
                'status' => 'ativo'
            ];
        }

        return [
            'nome' => 'Usuário Não Encontrado',
            'email' => 'N/A',
            'telefone' => 'N/A',
            'deletado_em' => null,
            'status' => 'nao_encontrado'
        ];
    }

    /**
     * Scope para trials com usuários deletados
     */
    public function scopeComUsuarioDeletado($query)
    {
        return $query->whereNotNull('deletado_em');
    }

    /**
     * Scope para trials com usuários ativos
     */
    public function scopeComUsuarioAtivo($query)
    {
        return $query->whereNull('deletado_em');
    }
}