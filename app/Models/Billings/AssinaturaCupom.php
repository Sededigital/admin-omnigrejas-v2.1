<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssinaturaCupom extends Model
{
    protected $table = 'assinatura_cupons';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'descricao',
        'desconto_percentual',
        'desconto_valor',
        'valido_de',
        'valido_ate',
        'uso_max',
        'usado',
        'ativo',
    ];

    protected $casts = [
        'desconto_valor'     => 'decimal:2',
        'valido_de'          => 'date',
        'valido_ate'         => 'date',
        'ativo'              => 'boolean',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    public function usos(): HasMany
    {
        return $this->hasMany(AssinaturaCupomUso::class, 'cupom_id');
    }

    public function assinaturas(): HasMany
    {
        return $this->hasMany(AssinaturaCupomUso::class, 'cupom_id');
    }

    // 🔗 MÉTODOS DE VALIDAÇÃO
    public function isValido(): bool
    {
        if (!$this->ativo) {
            return false;
        }

        $hoje = now();

        if ($this->valido_de && $hoje < $this->valido_de) {
            return false;
        }

        if ($this->valido_ate && $hoje > $this->valido_ate) {
            return false;
        }

        if ($this->uso_max && $this->usado >= $this->uso_max) {
            return false;
        }

        return true;
    }

    public function isExpirado(): bool
    {
        return $this->valido_ate && now() > $this->valido_ate;
    }

    public function isLimitado(): bool
    {
        return $this->uso_max > 0;
    }

    public function isDisponivel(): bool
    {
        return $this->isValido() && !$this->isExpirado();
    }

    public function getDescontoFormatado(): string
    {
        if ($this->desconto_percentual) {
            return $this->desconto_percentual . '%';
        }

        if ($this->desconto_valor) {
            return 'Kz ' . number_format($this->desconto_valor, 2, ',', '.');
        }

        return 'Sem desconto';
    }

    public function getValidadeFormatada(): string
    {
        if ($this->valido_ate) {
            return $this->valido_ate->format('d/m/Y');
        }

        return 'Ilimitado';
    }

    public function getUsoFormatado(): string
    {
        if ($this->uso_max) {
            return $this->usado . '/' . $this->uso_max;
        }

        return $this->usado . ' (ilimitado)';
    }

    public function getPercentualUso(): float
    {
        if (!$this->uso_max) {
            return 0;
        }

        return round(($this->usado / $this->uso_max) * 100, 2);
    }

    public function podeSerUsado(): bool
    {
        return $this->isDisponivel() && (!$this->isLimitado() || $this->usado < $this->uso_max);
    }
}
