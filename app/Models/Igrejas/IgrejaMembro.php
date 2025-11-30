<?php

namespace App\Models\Igrejas;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\Ministerio;
use App\Models\Igrejas\MembroPerfil;
use App\Models\Igrejas\AliancaIgreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Igrejas\IgrejaMembrosMinisterio;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaMembro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'igreja_membros';
    protected $primaryKey = 'id';
    public $incrementing = false;  // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'igreja_id',
        'user_id',
        'cargo',
        'status',
        'data_entrada',
        'numero_membro',
        'principal',
        'created_by',
    ];

    protected $casts = [
        'data_entrada' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function membroPerfil(): HasOne
    {
        return $this->hasOne(MembroPerfil::class, 'igreja_membro_id');
    }

    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'igreja_membros_ministerios', 'membro_id', 'ministerio_id')
                    ->using(IgrejaMembrosMinisterio::class)
                    ->withPivot('funcao');
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voluntario(): HasOne
    {
        return $this->hasOne(Voluntario::class, 'membro_id');
    }


    // Helpers para cargo
    public function isPastor(): bool
    {
        return $this->cargo === 'pastor';
    }

    public function isAdmin(): bool
    {
        return $this->cargo === 'admin';
    }

    public function isMinistro(): bool
    {
        return $this->cargo === 'ministro';
    }

    public function isObreiro(): bool
    {
        return $this->cargo === 'obreiro';
    }

    public function isDiacono(): bool
    {
        return $this->cargo === 'diacono';
    }

    public function isMembro(): bool
    {
        return $this->cargo === 'membro';
    }

    // Helpers para status
    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function isInativo(): bool
    {
        return $this->status === 'inativo';
    }

    public function isPrincipal(): bool
    {
        return $this->principal === true;
    }

    // ========================================
    // ATUALIZAÇÃO AUTOMÁTICA DE CONTADORES
    // ========================================

    protected static function booted()
    {
        // Quando um membro é criado
        static::created(function ($membro) {
            if ($membro->status === 'ativo') {
                $membro->atualizarContadoresAliancas();
            }
        });

        // Quando um membro é ativado
        static::updated(function ($membro) {
            // Se o status mudou para ativo
            if ($membro->status === 'ativo' && $membro->getOriginal('status') !== 'ativo') {
                $membro->atualizarContadoresAliancas();
            }
            // Se o status mudou de ativo para inativo
            elseif ($membro->status === 'inativo' && $membro->getOriginal('status') === 'ativo') {
                $membro->removerDosContadoresAliancas();
            }
        });

        // Quando um membro é excluído
        static::deleted(function ($membro) {
            if ($membro->getOriginal('status') === 'ativo') {
                $membro->removerDosContadoresAliancas();
            }
        });
    }

    public function atualizarContadoresAliancas()
    {
        // Buscar alianças das quais a igreja deste membro participa
        $aliancas = AliancaIgreja::whereHas('participacoes', function($query) {
            $query->where('igreja_id', $this->igreja_id)
                  ->where('status', 'ativo');
        })->get();

        // Atualizar contador de cada aliança
        foreach ($aliancas as $alianca) {
            $alianca->fresh()->atualizarContadorAderentes();
        }
    }

    public function removerDosContadoresAliancas()
    {
        // Buscar alianças das quais a igreja deste membro participa
        $aliancas = AliancaIgreja::whereHas('participacoes', function($query) {
            $query->where('igreja_id', $this->igreja_id)
                  ->where('status', 'ativo');
        })->get();

        // Atualizar contador de cada aliança
        foreach ($aliancas as $alianca) {
            $alianca->fresh()->atualizarContadorAderentes();
        }
    }

}
