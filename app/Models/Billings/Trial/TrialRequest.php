<?php

namespace App\Models\Billings\Trial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialRequest extends Model
{
    use HasFactory;

    protected $table = 'trial_requests';

    protected $fillable = [
        'nome',
        'email',
        'password',
        'igreja_nome',
        'denominacao',
        'telefone',
        'cidade',
        'provincia',
        'periodo_dias',
        'status',
        'aprovado_por',
        'aprovado_em',
        'rejeitado_por',
        'rejeitado_em',
        'motivo_rejeicao',
        'observacoes',
    ];

    protected $casts = [
        'aprovado_em' => 'datetime',
        'rejeitado_em' => 'datetime',
        'periodo_dias' => 'integer',
    ];

    // Status possíveis
    const STATUS_PENDENTE = 'pendente';
    const STATUS_APROVADO = 'aprovado';
    const STATUS_REJEITADO = 'rejeitado';

    // Scopes
    public function scopePendente($query)
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    public function scopeAprovado($query)
    {
        return $query->where('status', self::STATUS_APROVADO);
    }

    public function scopeRejeitado($query)
    {
        return $query->where('status', self::STATUS_REJEITADO);
    }

    // Relationships
    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'aprovado_por');
    }

    public function rejeitadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'rejeitado_por');
    }

    // Métodos auxiliares
    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    public function isAprovado(): bool
    {
        return $this->status === self::STATUS_APROVADO;
    }

    public function isRejeitado(): bool
    {
        return $this->status === self::STATUS_REJEITADO;
    }

    public function aprovar(\App\Models\User $user, string $observacoes = null): bool
    {
        $this->update([
            'status' => self::STATUS_APROVADO,
            'aprovado_por' => $user->id,
            'aprovado_em' => now(),
            'observacoes' => $observacoes,
        ]);

        return true;
    }

    public function rejeitar(\App\Models\User $user, string $motivo, string $observacoes = null): bool
    {
        $this->update([
            'status' => self::STATUS_REJEITADO,
            'rejeitado_por' => $user->id,
            'rejeitado_em' => now(),
            'motivo_rejeicao' => $motivo,
            'observacoes' => $observacoes,
        ]);

        return true;
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_APROVADO => 'Aprovado',
            self::STATUS_REJEITADO => 'Rejeitado',
        ];
    }
}