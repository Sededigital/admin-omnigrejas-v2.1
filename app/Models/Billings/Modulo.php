<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Billings\PacotePermissao;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function permissoesPacotes(): HasMany
    {
        return $this->hasMany(PacotePermissao::class, 'modulo_id');
    }

    public function pacotes(): HasMany
    {
        return $this->hasMany(PacotePermissao::class, 'modulo_id');
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function getNomeFormatado(): string
    {
        return ucfirst($this->nome);
    }

    public function getDescricaoFormatada(): string
    {
        return $this->descricao ?: 'Sem descrição';
    }

    public function isFinanceiro(): bool
    {
        return $this->nome === 'financeiro';
    }

    public function isIgrejas(): bool
    {
        return $this->nome === 'igrejas';
    }

    public function isCursos(): bool
    {
        return $this->nome === 'cursos';
    }

    public function isEventos(): bool
    {
        return $this->nome === 'eventos';
    }

    public function isComunicacao(): bool
    {
        return $this->nome === 'comunicacao';
    }

    public function isSocial(): bool
    {
        return $this->nome === 'social';
    }

    public function isPastoral(): bool
    {
        return $this->nome === 'pastoral';
    }

    public function isGamificacao(): bool
    {
        return $this->nome === 'gamificacao';
    }

    public function getIcone(): string
    {
        return match($this->nome) {
            'financeiro' => 'fas fa-chart-line',
            'igrejas' => 'fas fa-church',
            'cursos' => 'fas fa-graduation-cap',
            'eventos' => 'fas fa-calendar-alt',
            'comunicacao' => 'fas fa-comments',
            'social' => 'fas fa-users',
            'pastoral' => 'fas fa-hands-helping',
            'gamificacao' => 'fas fa-trophy',
            default => 'fas fa-cube'
        };
    }

    public function getCor(): string
    {
        return match($this->nome) {
            'financeiro' => 'success',
            'igrejas' => 'primary',
            'cursos' => 'info',
            'eventos' => 'warning',
            'comunicacao' => 'secondary',
            'social' => 'danger',
            'pastoral' => 'dark',
            'gamificacao' => 'warning',
            default => 'secondary'
        };
    }
}
