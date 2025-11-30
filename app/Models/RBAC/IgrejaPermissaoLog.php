<?php

namespace App\Models\RBAC;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaPermissaoLog extends Model
{
    use HasFactory;

    protected $table = 'igreja_permissao_logs';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'igreja_id',
        'membro_id',
        'funcao_id',
        'permissao_id',
        'acao',
        'detalhes',
        'realizado_por',
        'realizado_em',
    ];

    protected $casts = [
        'detalhes' => 'json',
        'realizado_em' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'membro_id');
    }

    public function funcao(): BelongsTo
    {
        return $this->belongsTo(IgrejaFuncao::class, 'funcao_id');
    }

    public function permissao(): BelongsTo
    {
        return $this->belongsTo(IgrejaPermissao::class, 'permissao_id');
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }

    // ========================================
    // HELPERS PARA CHECAGEM RÁPIDA
    // ========================================
    public function isAtribuicaoFuncao(): bool
    {
        return $this->acao === 'atribuir_funcao';
    }

    public function isRevogacaoFuncao(): bool
    {
        return $this->acao === 'revogar_funcao';
    }

    public function isAlteracaoPermissao(): bool
    {
        return $this->acao === 'alterar_permissao';
    }

    public function foiRealizadoRecentemente(): bool
    {
        return $this->realizado_em->diffInHours(now()) <= 24;
    }

    public function getAcaoLabel(): string
    {
        return match($this->acao) {
            'atribuir_funcao' => 'Atribuição de Função',
            'revogar_funcao' => 'Revogação de Função',
            'alterar_permissao' => 'Alteração de Permissão',
            default => ucfirst(str_replace('_', ' ', $this->acao ?? 'Desconhecido'))
        };
    }

    public function getTempoDesdeAcao(): string
    {
        return $this->realizado_em->diffForHumans();
    }

    public function temDetalhes(): bool
    {
        return !empty($this->detalhes);
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopePorMembro($query, $membroId)
    {
        return $query->where('membro_id', $membroId);
    }

    public function scopePorFuncao($query, $funcaoId)
    {
        return $query->where('funcao_id', $funcaoId);
    }

    public function scopePorPermissao($query, $permissaoId)
    {
        return $query->where('permissao_id', $permissaoId);
    }

    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    public function scopeRealizadosPor($query, $userId)
    {
        return $query->where('realizado_por', $userId);
    }

    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('realizado_em', '>=', now()->subDays($dias));
    }

    public function scopeAntigos($query, $dias = 30)
    {
        return $query->where('realizado_em', '<', now()->subDays($dias));
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('realizado_em', today());
    }

    public function scopeEstaSemana($query)
    {
        return $query->where('realizado_em', '>=', now()->startOfWeek());
    }

    public function scopeEsteMes($query)
    {
        return $query->where('realizado_em', '>=', now()->startOfMonth());
    }

    // ========================================
    // MÉTODOS DE NEGÓCIO
    // ========================================
    public function getDescricaoCompleta(): string
    {
        $descricao = $this->getAcaoLabel();

        if ($this->membro && $this->funcao) {
            $membroNome = $this->membro->user->name ?? 'Membro';
            $funcaoNome = $this->funcao->nome;
            $descricao .= ": {$membroNome} - {$funcaoNome}";
        }

        if ($this->permissao) {
            $permissaoNome = $this->permissao->nome;
            $descricao .= " ({$permissaoNome})";
        }

        return $descricao;
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================
    public static function logAtribuicaoFuncao(
        IgrejaMembro $membro,
        IgrejaFuncao $funcao,
        User $realizadoPor,
        array $detalhes = []
    ): bool {
        return static::create([
            'igreja_id' => $membro->igreja_id,
            'membro_id' => $membro->id,
            'funcao_id' => $funcao->id,
            'acao' => 'atribuir_funcao',
            'detalhes' => array_merge($detalhes, [
                'membro_nome' => $membro->user->name ?? 'Desconhecido',
                'funcao_nome' => $funcao->nome,
                'nivel_hierarquia' => $funcao->nivel_hierarquia,
            ]),
            'realizado_por' => $realizadoPor->id,
            'realizado_em' => now(),
        ]) instanceof static;
    }

    public static function logRevogacaoFuncao(
        IgrejaMembro $membro,
        IgrejaFuncao $funcao,
        User $realizadoPor,
        array $detalhes = []
    ): bool {
        return static::create([
            'igreja_id' => $membro->igreja_id,
            'membro_id' => $membro->id,
            'funcao_id' => $funcao->id,
            'acao' => 'revogar_funcao',
            'detalhes' => array_merge($detalhes, [
                'membro_nome' => $membro->user->name ?? 'Desconhecido',
                'funcao_nome' => $funcao->nome,
                'motivo' => $detalhes['motivo'] ?? 'Não informado',
            ]),
            'realizado_por' => $realizadoPor->id,
            'realizado_em' => now(),
        ]) instanceof static;
    }

    public static function logAlteracaoPermissao(
        IgrejaFuncao $funcao,
        IgrejaPermissao $permissao,
        User $realizadoPor,
        string $tipoAlteracao,
        array $detalhes = []
    ): bool {
        return static::create([
            'igreja_id' => $funcao->igreja_id,
            'funcao_id' => $funcao->id,
            'permissao_id' => $permissao->id,
            'acao' => 'alterar_permissao',
            'detalhes' => array_merge($detalhes, [
                'funcao_nome' => $funcao->nome,
                'permissao_nome' => $permissao->nome,
                'tipo_alteracao' => $tipoAlteracao,
            ]),
            'realizado_por' => $realizadoPor->id,
            'realizado_em' => now(),
        ]) instanceof static;
    }

    public static function estatisticasPorIgreja($igrejaId, $dias = 30)
    {
        return static::porIgreja($igrejaId)
            ->recientes($dias)
            ->selectRaw('acao, COUNT(*) as total')
            ->groupBy('acao')
            ->pluck('total', 'acao')
            ->toArray();
    }

    public static function atividadesRecentes($igrejaId, $limite = 10)
    {
        return static::porIgreja($igrejaId)
            ->with(['membro.user', 'funcao', 'permissao', 'realizadoPor'])
            ->orderBy('realizado_em', 'desc')
            ->limit($limite)
            ->get();
    }

    // ========================================
    // BOOT METHOD
    // ========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->id)) {
                $log->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
