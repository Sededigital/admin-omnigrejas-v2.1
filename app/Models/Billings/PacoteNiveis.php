<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PacoteNiveis extends Model
{
    use HasFactory;

    protected $table = 'pacote_niveis';

    protected $fillable = [
        'pacote_id',
        'nivel',
        'prioridade',
        'recursos_extras',
    ];

    protected $casts = [
        'prioridade' => 'integer',
        'recursos_extras' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_id');
    }

    // 🔗 SCOPES
    public function scopePorPrioridade($query, $ordem = 'asc')
    {
        return $query->orderBy('prioridade', $ordem);
    }

    public function scopeMaioresOuIguais($query, $prioridade)
    {
        return $query->where('prioridade', '>=', $prioridade);
    }

    public function scopeMenoresOuIguais($query, $prioridade)
    {
        return $query->where('prioridade', '<=', $prioridade);
    }

    // 🔗 HELPERS
    public function getNomeFormatadoAttribute(): string
    {
        return ucfirst($this->nivel);
    }

    public function isMaiorOuIgual(PacoteNiveis $outroNivel): bool
    {
        return $this->prioridade >= $outroNivel->prioridade;
    }

    public function isMenorOuIgual(PacoteNiveis $outroNivel): bool
    {
        return $this->prioridade <= $outroNivel->prioridade;
    }

    public function getBadgeClass(): string
    {
        return match($this->nivel) {
            'basico' => 'secondary',
            'profissional' => 'info',
            'premium' => 'primary',
            'enterprise' => 'success',
            default => 'light'
        };
    }

    public function getIcone(): string
    {
        return match($this->nivel) {
            'basico' => 'fas fa-star-half-alt',
            'profissional' => 'fas fa-star',
            'premium' => 'fas fa-crown',
            'enterprise' => 'fas fa-gem',
            default => 'fas fa-star'
        };
    }

    public function getDescricao(): string
    {
        return match($this->nivel) {
            'basico' => 'Funcionalidades essenciais',
            'profissional' => 'Recursos avançados',
            'premium' => 'Ferramentas completas',
            'enterprise' => 'Soluções empresariais',
            default => 'Nível personalizado'
        };
    }

    public function temRecursosExtras(): bool
    {
        return !empty($this->recursos_extras) && is_array($this->recursos_extras);
    }

    public function getRecursosExtrasFormatados(): array
    {
        if (!$this->temRecursosExtras()) {
            return [];
        }

        return array_map(function($recurso) {
            return is_string($recurso) ? $recurso : json_encode($recurso);
        }, $this->recursos_extras);
    }
}
