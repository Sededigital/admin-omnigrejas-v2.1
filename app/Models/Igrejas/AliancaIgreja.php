<?php

namespace App\Models\Igrejas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Igrejas\IgrejaAlianca;
use App\Models\Igrejas\IgrejaMembro;

class AliancaIgreja extends Model
{
    use HasFactory;

    protected $table = 'aliancas_igrejas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nome',
        'sigla',
        'descricao',
        'ativa',
        'categoria_id',
        'status',
        'created_by',
        'aprovado_by',
        'aprovado_em',
        'limite_membros',
        'aderentes_count',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'limite_membros' => 'integer',
        'aderentes_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'aprovado_em' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaIgreja::class, 'categoria_id');
    }

    public function participacoes(): HasMany
    {
        return $this->hasMany(IgrejaAlianca::class, 'alianca_id');
    }

    public function igrejasParticipantes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Igreja::class,
            IgrejaAlianca::class,
            'alianca_id', // Foreign key on IgrejaAlianca table
            'id', // Foreign key on Igreja table
            'id', // Local key on AliancaIgreja table
            'igreja_id' // Local key on IgrejaAlianca table
        )->where('igreja_aliancas.status', 'ativo');
    }

    // Relacionamento muitos-para-muitos com igrejas via tabela pivot
    public function igrejas()
    {
        return $this->belongsToMany(Igreja::class, 'igreja_aliancas', 'alianca_id', 'igreja_id')
                    ->withPivot(['status', 'data_adesao', 'data_desligamento', 'observacoes'])
                    ->withTimestamps();
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'aprovado_by');
    }

    // 🔗 RELACIONAMENTOS ESPECIAIS
    public function igrejasAtivas(): HasMany
    {
        return $this->igrejas()->where('status_aprovacao', 'aprovado');
    }

    public function lideres(): HasManyThrough
    {
        return $this->hasManyThrough(
            AliancaLider::class,
            IgrejaAlianca::class,
            'alianca_id', // Foreign key on IgrejaAlianca table
            'igreja_alianca_id', // Foreign key on AliancaLider table
            'id', // Local key on AliancaIgreja table
            'id' // Local key on IgrejaAlianca table
        );
    }

    public function lideresAtivos(): HasManyThrough
    {
        return $this->lideres()->where('alianca_lideres.ativo', true);
    }

    // ========================================
    // CONSTANTES DE STATUS
    // ========================================
    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_PENDENTE_VALIDACAO = 'pendente_validacao';
    public const STATUS_PRONTA_APROVACAO = 'pronta_aprovacao';
    public const STATUS_APROVADA = 'aprovada';
    public const STATUS_REJEITADA = 'rejeitada';
    public const STATUS_SUSPENSA = 'suspensa';

    public const MIN_ADERENTES_PADRAO = 2;

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isAtiva(): bool
    {
        return $this->ativa === true;
    }

    public function isAprovada(): bool
    {
        return $this->status === self::STATUS_APROVADA;
    }

    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE_VALIDACAO;
    }

    public function podeSerAprovada(): bool
    {
        // Agora a aprovação é baseada na adesão da igreja (campo alianca_id na tabela igrejas)
        // O Super Admin tem controle total sobre quando aprovar
        return $this->status === self::STATUS_PRONTA_APROVACAO;
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }

    public function scopeAprovadas($query)
    {
        return $query->where('status', self::STATUS_APROVADA);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', self::STATUS_PENDENTE_VALIDACAO);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->sigla ? "{$this->nome} ({$this->sigla})" : $this->nome;
    }

    // ========================================
    // MÉTODOS DE VALIDAÇÃO E MATCHING
    // ========================================
    public function validarUnicidade($nome, $categoriaId)
    {
        return !static::where('nome', 'ILIKE', $nome)
            ->where('categoria_id', $categoriaId)
            ->where('status', '!=', self::STATUS_REJEITADA)
            ->exists();
    }

    public function encontrarIgrejasCompativeis()
    {
        if (!$this->categoria_id) {
            return collect();
        }

        return Igreja::where('categoria_id', $this->categoria_id)
            ->where('status_aprovacao', 'aprovado')
            ->where('id', '!=', $this->criador?->getIgreja()?->id)
            ->get();
    }

    public function atualizarContadorAderentes()
    {
        // Conta o total de membros das igrejas participantes ativas usando query direta
        $totalMembros = DB::table('igreja_membros')
            ->join('igrejas', 'igreja_membros.igreja_id', '=', 'igrejas.id')
            ->join('igreja_aliancas', function($join) {
                $join->on('igrejas.id', '=', 'igreja_aliancas.igreja_id')
                     ->where('igreja_aliancas.alianca_id', '=', $this->id)
                     ->where('igreja_aliancas.status', '=', 'ativo');
            })
            ->where('igreja_membros.status', 'ativo')
            ->count();

        $this->aderentes_count = $totalMembros;
        $this->save();

        // O status é controlado pelo Super Admin, não mais automaticamente
        // As igrejas podem aderir livremente através do campo alianca_id
    }

    public function adicionarIgreja($igrejaId, $observacoes = null)
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            // Verificar se já existe participação ativa
            $participacaoExistente = IgrejaAlianca::where('igreja_id', $igrejaId)
                ->where('alianca_id', $this->id)
                ->where('status', 'ativo')
                ->first();

            if (!$participacaoExistente) {
                IgrejaAlianca::create([
                    'igreja_id' => $igrejaId,
                    'alianca_id' => $this->id,
                    'status' => 'ativo',
                    'observacoes' => $observacoes,
                    'created_by' => Auth::id(),
                ]);
                $this->atualizarContadorAderentes();
            }
        }
    }

    public function removerIgreja($igrejaId, $motivo = null)
    {
        $participacao = IgrejaAlianca::where('igreja_id', $igrejaId)
            ->where('alianca_id', $this->id)
            ->where('status', 'ativo')
            ->first();

        if ($participacao) {
            $participacao->desligar($motivo);
            $this->fresh()->atualizarContadorAderentes();
        }
    }
}
