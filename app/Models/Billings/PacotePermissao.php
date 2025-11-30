<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Billings\Pacote;
use App\Models\Billings\Modulo;

class PacotePermissao extends Model
{
    use HasFactory;

    protected $table = 'pacote_permissao';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pacote_id',
        'modulo_id',
        'permissao',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_id');
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    // 🔗 MÉTODOS DE CONVENIÊNCIA
    public function isLeitura(): bool
    {
        return $this->permissao === 'leitura';
    }

    public function isEscrita(): bool
    {
        return $this->permissao === 'escrita';
    }

    public function isNenhuma(): bool
    {
        return $this->permissao === 'nenhuma';
    }

    public function getPermissaoFormatada(): string
    {
        return match($this->permissao) {
            'leitura' => 'Leitura',
            'escrita' => 'Escrita',
            'nenhuma' => 'Nenhuma',
            default => ucfirst($this->permissao)
        };
    }

    public function getPermissaoClass(): string
    {
        return match($this->permissao) {
            'leitura' => 'info',
            'escrita' => 'success',
            'nenhuma' => 'secondary',
            default => 'secondary'
        };
    }

    public function getPermissaoIcone(): string
    {
        return match($this->permissao) {
            'leitura' => 'fas fa-eye',
            'escrita' => 'fas fa-edit',
            'nenhuma' => 'fas fa-ban',
            default => 'fas fa-question'
        };
    }

    public function podeLer(): bool
    {
        return $this->isLeitura() || $this->isEscrita();
    }

    public function podeEscrever(): bool
    {
        return $this->isEscrita();
    }

    public function temPermissao(): bool
    {
        return !$this->isNenhuma();
    }

    public function getDescricaoPermissao(): string
    {
        return match($this->permissao) {
            'leitura' => 'Pode visualizar informações',
            'escrita' => 'Pode visualizar e modificar informações',
            'nenhuma' => 'Sem acesso a este módulo',
            default => 'Permissão desconhecida'
        };
    }
}
