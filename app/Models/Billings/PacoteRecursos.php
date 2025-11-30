<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PacoteRecursos extends Model
{
    use HasFactory;

    protected $table = 'pacote_recursos';

    protected $fillable = [
        'pacote_id',
        'recurso_tipo',
        'limite_valor',
        'unidade',
        'ativo',
    ];

    protected $casts = [
        'limite_valor' => 'integer',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Logo após os casts
    protected function limiteValor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) =>
                ($value === '' || $value === null) ? null : (int) $value
        );
    }

    // 🔗 RELACIONAMENTOS
    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_id');
    }

    // 🔗 SCOPES
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('recurso_tipo', $tipo);
    }

    public function scopeIlimitados($query)
    {
        return $query->whereNull('limite_valor');
    }

    public function scopeLimitados($query)
    {
        return $query->whereNotNull('limite_valor');
    }

    // 🔗 HELPERS
    public function isIlimitado(): bool
    {
        return is_null($this->limite_valor);
    }

    public function isLimitado(): bool
    {
        return !is_null($this->limite_valor);
    }

    public function getLimiteFormatado(): string
    {
        if ($this->isIlimitado()) {
            return 'Ilimitado';
        }

        return number_format($this->limite_valor, 0, ',', '.') . ' ' . $this->unidade;
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

    public function getDescricao(): string
    {
        try {
            $permissao = \App\Models\RBAC\IgrejaPermissao::where('codigo', $this->recurso_tipo)->first();
            return $permissao ? $permissao->descricao : 'Permissão: ' . $this->getTipoFormatado();
        } catch (\Exception $e) {
            return 'Permissão: ' . $this->getTipoFormatado();
        }
    }
}
