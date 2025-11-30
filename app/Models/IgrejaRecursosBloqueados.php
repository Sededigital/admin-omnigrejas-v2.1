<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Igrejas\Igreja;
use App\Models\User;

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

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('recurso_tipo', $tipo);
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

    public function isInativo(): bool
    {
        return !$this->ativo;
    }

    public function foiDesbloqueado(): bool
    {
        return !is_null($this->desbloqueado_em);
    }

    public function getTipoFormatado(): string
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

    public function getStatusClass(): string
    {
        return $this->isAtivo() ? 'danger' : 'success';
    }

    public function getStatusText(): string
    {
        return $this->isAtivo() ? 'Bloqueado' : 'Desbloqueado';
    }

    public function getBloqueadoEmFormatado(): string
    {
        return $this->bloqueado_em->format('d/m/Y H:i');
    }

    public function getDesbloqueadoEmFormatado(): string
    {
        return $this->desbloqueado_em ? $this->desbloqueado_em->format('d/m/Y H:i') : 'Não desbloqueado';
    }

    public function getBloqueadoEmRelativo(): string
    {
        return $this->bloqueado_em->diffForHumans();
    }

    public function getDesbloqueadoEmRelativo(): string
    {
        return $this->desbloqueado_em ? $this->desbloqueado_em->diffForHumans() : 'Não desbloqueado';
    }

    public function getDiasBloqueado(): int
    {
        if ($this->foiDesbloqueado()) {
            return $this->bloqueado_em->diffInDays($this->desbloqueado_em);
        }

        return $this->bloqueado_em->diffInDays(now());
    }

    public function bloquear($motivo, $usuarioId): void
    {
        $this->update([
            'motivo_bloqueio' => $motivo,
            'bloqueado_por' => $usuarioId,
            'bloqueado_em' => now(),
            'desbloqueado_em' => null,
            'ativo' => true,
        ]);
    }

    public function desbloquear($usuarioId): void
    {
        $this->update([
            'desbloqueado_em' => now(),
            'bloqueado_por' => $usuarioId,
            'ativo' => false,
        ]);
    }

    public function getMotivoResumido($limite = 50): string
    {
        if (strlen($this->motivo_bloqueio) <= $limite) {
            return $this->motivo_bloqueio;
        }

        return substr($this->motivo_bloqueio, 0, $limite) . '...';
    }
}
