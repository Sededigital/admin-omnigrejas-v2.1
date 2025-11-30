<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaRecursosBloqueados extends Model
{
    use HasFactory;

    protected $table = 'igreja_recursos_bloqueados';

    protected $fillable = [
        'igreja_id',
        'recurso_tipo',
        'motivo_bloqueio',
        'bloqueado_em',
        'desbloqueado_em',
        'bloqueado_por',
        'ativo',
    ];

    protected $casts = [
        'bloqueado_em' => 'datetime',
        'desbloqueado_em' => 'datetime',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function bloqueadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bloqueado_por');
    }

    // 🔗 SCOPES
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    public function scopePorRecurso($query, $recursoTipo)
    {
        return $query->where('recurso_tipo', $recursoTipo);
    }

    public function scopeBloqueadosRecentemente($query, $dias = 7)
    {
        return $query->where('bloqueado_em', '>=', now()->subDays($dias));
    }

    public function scopeDesbloqueadosRecentemente($query, $dias = 7)
    {
        return $query->where('desbloqueado_em', '>=', now()->subDays($dias));
    }

    // 🔗 HELPERS
    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function isBloqueado(): bool
    {
        return $this->isAtivo();
    }

    public function isDesbloqueado(): bool
    {
        return !$this->isAtivo();
    }

    public function foiDesbloqueado(): bool
    {
        return !is_null($this->desbloqueado_em);
    }

    public function bloquear($motivo, User $usuario): void
    {
        $this->update([
            'motivo_bloqueio' => $motivo,
            'bloqueado_em' => now(),
            'bloqueado_por' => $usuario->id,
            'ativo' => true,
            'desbloqueado_em' => null
        ]);
    }

    public function desbloquear(User $usuario): void
    {
        $this->update([
            'ativo' => false,
            'desbloqueado_em' => now()
        ]);
    }

    public function getRecursoFormatado(): string
    {
        try {
            $permissao = \App\Models\RBAC\IgrejaPermissao::where('codigo', $this->recurso_tipo)->first();
            return $permissao ? $permissao->nome : ucfirst($this->recurso_tipo);
        } catch (\Exception $e) {
            return ucfirst($this->recurso_tipo);
        }
    }

    public function getIcone(): string
    {
        return 'fas fa-cogs'; // Ícone genérico para permissões
    }

    public function getStatusFormatado(): string
    {
        return $this->isAtivo() ? 'Bloqueado' : 'Desbloqueado';
    }

    public function getStatusBadgeClass(): string
    {
        return $this->isAtivo() ? 'danger' : 'success';
    }

    public function getBloqueadoEmFormatado(): string
    {
        return $this->bloqueado_em->format('d/m/Y H:i');
    }

    public function getDesbloqueadoEmFormatado(): ?string
    {
        return $this->desbloqueado_em ? $this->desbloqueado_em->format('d/m/Y H:i') : null;
    }

    public function getTempoBloqueado(): ?string
    {
        if (!$this->bloqueado_em) {
            return null;
        }

        $dataFim = $this->desbloqueado_em ?? now();
        $diferenca = $this->bloqueado_em->diff($dataFim);

        if ($diferenca->days > 0) {
            return $diferenca->days . ' dias';
        }

        if ($diferenca->h > 0) {
            return $diferenca->h . ' horas';
        }

        return $diferenca->i . ' minutos';
    }

    public function getMotivoResumido($limite = 50): string
    {
        if (strlen($this->motivo_bloqueio) <= $limite) {
            return $this->motivo_bloqueio;
        }

        return substr($this->motivo_bloqueio, 0, $limite) . '...';
    }
}
