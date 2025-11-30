<?php

namespace App\Models\Outros;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EngajamentoPonto extends Model
{
    use HasFactory;

    protected $table = 'engajamento_pontos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'igreja_id',
        'pontos',
        'motivo',
        'data',
    ];

    protected $casts = [
        'pontos' => 'integer',
        'data' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(EngajamentoBadge::class, 'user_id', 'user_id');
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function isPositivo(): bool
    {
        return $this->pontos > 0;
    }

    public function isNegativo(): bool
    {
        return $this->pontos < 0;
    }

    public function isNeutro(): bool
    {
        return $this->pontos === 0;
    }

    public function getPontosFormatados(): string
    {
        if ($this->isPositivo()) {
            return '+' . $this->pontos;
        }

        return $this->pontos;
    }

    public function getPontosClass(): string
    {
        if ($this->isPositivo()) {
            return 'success';
        }

        if ($this->isNegativo()) {
            return 'danger';
        }

        return 'secondary';
    }

    public function getPontosIcone(): string
    {
        if ($this->isPositivo()) {
            return 'fas fa-plus-circle';
        }

        if ($this->isNegativo()) {
            return 'fas fa-minus-circle';
        }

        return 'fas fa-circle';
    }

    public function getDataFormatada(): string
    {
        return $this->data->format('d/m/Y H:i');
    }

    public function getDataRelativa(): string
    {
        return $this->data->diffForHumans();
    }

    public function getMotivoFormatado(): string
    {
        return ucfirst($this->motivo ?: 'Sem motivo');
    }

    public function isRecente(int $dias = 7): bool
    {
        return $this->data->diffInDays(now()) <= $dias;
    }

    public function getDiasDesdePontuacao(): int
    {
        return $this->data->diffInDays(now());
    }

    public function getValorAbsoluto(): int
    {
        return abs($this->pontos);
    }

    public function getTipoPontuacao(): string
    {
        if ($this->isPositivo()) {
            return 'Ganhou';
        }

        if ($this->isNegativo()) {
            return 'Perdeu';
        }

        return 'Neutro';
    }
}
