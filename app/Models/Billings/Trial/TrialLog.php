<?php

namespace App\Models\Billings\Trial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class TrialLog extends Model
{
    use HasFactory;

    protected $table = 'trial_logs';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'trial_user_id',
        'acao',
        'descricao',
        'dados',
        'realizado_por',
        'realizado_em',
    ];

    protected $casts = [
        'dados' => 'array',
        'realizado_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function trialUser(): BelongsTo
    {
        return $this->belongsTo(TrialUser::class, 'trial_user_id');
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }

    // 🔗 SCOPES
    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('realizado_por', $userId);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeOntem($query)
    {
        return $query->whereDate('created_at', today()->subDay());
    }

    public function scopeEstaSemana($query)
    {
        return $query->where('created_at', '>=', now()->startOfWeek());
    }

    public function scopeEsteMes($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth());
    }

    public function scopePorPeriodo($query, $inicio, $fim)
    {
        return $query->whereBetween('created_at', [$inicio, $fim]);
    }

    // 🔗 HELPERS
    public function getAcaoFormatada(): string
    {
        return match($this->acao) {
            'criado' => 'Criado',
            'acessado' => 'Acessado',
            'expirado' => 'Expirado',
            'bloqueado' => 'Bloqueado',
            'reativado' => 'Reativado',
            'cancelado' => 'Cancelado',
            'limpo' => 'Limpo',
            'notificado' => 'Notificado',
            'dados_criados' => 'Dados Criados',
            'dados_deletados' => 'Dados Deletados',
            default => ucfirst(str_replace('_', ' ', $this->acao))
        };
    }

    public function getAcaoBadgeClass(): string
    {
        return match($this->acao) {
            'criado' => 'success',
            'acessado' => 'info',
            'expirado' => 'danger',
            'bloqueado' => 'dark',
            'reativado' => 'success',
            'cancelado' => 'secondary',
            'limpo' => 'warning',
            'notificado' => 'primary',
            'dados_criados' => 'success',
            'dados_deletados' => 'danger',
            default => 'secondary'
        };
    }

    public function getAcaoIcone(): string
    {
        return match($this->acao) {
            'criado' => 'fas fa-plus-circle',
            'acessado' => 'fas fa-eye',
            'expirado' => 'fas fa-times-circle',
            'bloqueado' => 'fas fa-ban',
            'reativado' => 'fas fa-check-circle',
            'cancelado' => 'fas fa-times',
            'limpo' => 'fas fa-trash',
            'notificado' => 'fas fa-bell',
            'dados_criados' => 'fas fa-plus',
            'dados_deletados' => 'fas fa-minus',
            default => 'fas fa-circle'
        };
    }

    public function getRealizadoEmFormatado(): string
    {
        return $this->realizado_em->format('d/m/Y H:i:s');
    }

    public function getRealizadoEmRelativo(): string
    {
        return $this->realizado_em->diffForHumans();
    }

    public function getCriadoEmFormatado(): string
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getCriadoEmRelativo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getDadosFormatados(): array
    {
        return $this->dados ?? [];
    }

    public function getValorDados($chave, $padrao = null)
    {
        $dados = $this->getDadosFormatados();
        return $dados[$chave] ?? $padrao;
    }

    public function foiRealizadoPorAdmin(): bool
    {
        return $this->realizado_por && $this->realizadoPor &&
               ($this->realizadoPor->isSuperAdmin() || $this->realizadoPor->isRoot());
    }

    public function foiRealizadoAutomaticamente(): bool
    {
        return is_null($this->realizado_por);
    }

    public function getRealizadoPorNome(): string
    {
        if ($this->foiRealizadoAutomaticamente()) {
            return 'Sistema Automático';
        }

        return $this->realizadoPor ? $this->realizadoPor->name : 'Usuário Desconhecido';
    }

    public function getDescricaoResumida($limite = 50): string
    {
        if (strlen($this->descricao) <= $limite) {
            return $this->descricao;
        }

        return substr($this->descricao, 0, $limite) . '...';
    }

    public function getDiasDesdeAcao(): int
    {
        return $this->realizado_em->diffInDays(now());
    }

    public function foiHoje(): bool
    {
        return $this->realizado_em->isToday();
    }

    public function foiOntem(): bool
    {
        return $this->realizado_em->isYesterday();
    }

    public function foiEstaSemana(): bool
    {
        return $this->realizado_em->isCurrentWeek();
    }

    public function foiEsteMes(): bool
    {
        return $this->realizado_em->isCurrentMonth();
    }

    // 🔗 MÉTODOS ESTÁTICOS PARA LOGS COMUNS
    public static function logCriacaoTrial(TrialUser $trial, User $criadoPor = null): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'criado',
            'descricao' => 'Trial criado para usuário ' . $trial->user->email,
            'dados' => [
                'user_email' => $trial->user->email,
                'igreja_nome' => $trial->igreja->nome,
                'periodo_dias' => $trial->periodo_dias,
            ],
            'realizado_por' => $criadoPor ? $criadoPor->id : null,
        ]);
    }

    public static function logAcessoTrial(TrialUser $trial): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'acessado',
            'descricao' => 'Usuário trial acessou o sistema',
            'dados' => [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);
    }

    public static function logExpiracaoTrial(TrialUser $trial): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'expirado',
            'descricao' => 'Trial expirado automaticamente',
            'dados' => [
                'data_expiracao' => $trial->data_fim->format('Y-m-d'),
                'dias_ativos' => $trial->diasDesdeCriacao(),
            ],
        ]);
    }

    public static function logReativacaoTrial(TrialUser $trial, User $admin): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'reativado',
            'descricao' => 'Trial reativado por administrador',
            'dados' => [
                'admin_nome' => $admin->name,
                'nova_data_fim' => $trial->data_fim->format('Y-m-d'),
            ],
            'realizado_por' => $admin->id,
        ]);
    }

    public static function logBloqueioTrial(TrialUser $trial, User $admin, string $motivo = null): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'bloqueado',
            'descricao' => 'Trial bloqueado por administrador',
            'dados' => [
                'admin_nome' => $admin->name,
                'motivo' => $motivo,
            ],
            'realizado_por' => $admin->id,
        ]);
    }

    public static function logCancelamentoTrial(TrialUser $trial, User $admin, string $motivo = null): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'cancelado',
            'descricao' => 'Trial cancelado por administrador',
            'dados' => [
                'admin_nome' => $admin->name,
                'motivo' => $motivo,
            ],
            'realizado_por' => $admin->id,
        ]);
    }

    public static function logDadosCriados(TrialUser $trial, string $tabela, string $registroId, string $tipoDado): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'dados_criados',
            'descricao' => "Dado criado: {$tipoDado} na tabela {$tabela}",
            'dados' => [
                'tabela' => $tabela,
                'registro_id' => $registroId,
                'tipo_dado' => $tipoDado,
            ],
        ]);
    }

    public static function logDadosDeletados(TrialUser $trial, string $tabela, string $registroId, string $tipoDado): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'dados_deletados',
            'descricao' => "Dado deletado: {$tipoDado} na tabela {$tabela}",
            'dados' => [
                'tabela' => $tabela,
                'registro_id' => $registroId,
                'tipo_dado' => $tipoDado,
            ],
        ]);
    }

    public static function logNotificacaoEnviada(TrialUser $trial, string $tipoAlerta): TrialLog
    {
        return static::create([
            'trial_user_id' => $trial->id,
            'acao' => 'notificado',
            'descricao' => "Notificação enviada: {$tipoAlerta}",
            'dados' => [
                'tipo_alerta' => $tipoAlerta,
            ],
        ]);
    }
}