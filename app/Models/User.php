<?php

namespace App\Models;

use App\Models\Chats\Post;
use App\Traits\HasAuditoria;
use App\Models\Eventos\Agenda;
use App\Models\Eventos\Escala;
use App\Models\Eventos\Evento;
use App\Models\Outros\Recurso;
use App\Models\Chats\Comentario;
use App\Models\Chats\IgrejaChat;
use App\Models\Chats\Comunicacao;
use App\Models\Chats\Notificacao;
use App\Models\Admin\AuditoriaLog;
use App\Models\Chats\PostReaction;
use App\Models\Eventos\EscalaAuto;
use App\Models\Igrejas\Ministerio;
use App\Models\Igrejas\Voluntario;
use App\Models\Outros\DoacaoOnline;
use App\Models\Admin\RelatorioCache;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Igrejas\MembroPerfil;
use App\Models\Pedidos\PedidoOracao;
use App\Models\Chats\MensagemPrivada;
use App\Models\Billings\AssinaturaLog;
use App\Models\Outros\EnqueteDenuncia;
use App\Models\Outros\EngajamentoBadge;
use App\Models\Outros\EngajamentoPonto;
use App\Models\Chats\IgrejaChatMensagem;
use Illuminate\Notifications\Notifiable;
use App\Models\Igrejas\HabilidadesMembro;
use App\Models\Billings\AssinaturaUpgrade;
use App\Models\Eventos\AgendamentoRecurso;
use App\Models\Igrejas\AtendimentoPastoral;
use App\Models\Marketplace\MarketplacePedido;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Financeiro\FinanceiroAuditoria;
use App\Models\Financeiro\FinanceiroMovimento;
use App\Models\Igrejas\IgrejaMembrosHistorico;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Igrejas\IgrejaMembrosMinisterio;
use App\Models\Marketplace\MarketplacePagamento;
use App\Models\Social\UserFollow;
use App\Models\Social\UserFollowActivity;
use App\Models\Social\UserFollowNotification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable  implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, HasAuditoria, TwoFactorAuthenticatable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = false;  // porque é UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'photo_url',
        'role',
        'denomination',
        'is_active',
        'status',
        'created_by',
    ];

     protected $hidden = [
        'is_active' => 'boolean',
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

      // ========================================
    // Constantes de Roles (igual ao ENUM do DB)
    // ========================================
    public const ROLE_ROOT       = 'root';
    public const ROLE_SUPERADMIN = 'super_admin';
    public const ROLE_ADMIN      = 'admin';       // igreja admin
    public const ROLE_PASTOR     = 'pastor';
    public const ROLE_MINISTRO   = 'ministro';
    public const ROLE_OBREIRO    = 'obreiro';
    public const ROLE_DIACONO    = 'diacono';
    public const ROLE_MEMBRO     = 'membro';
    public const ROLE_ANONYMOUS  = 'anonymous';

    // Array de todos os roles possíveis (para validação rápida)
    public const ROLES = [ 
        self::ROLE_ROOT,
        self::ROLE_SUPERADMIN,
        self::ROLE_ADMIN,
        self::ROLE_PASTOR,
        self::ROLE_MINISTRO,
        self::ROLE_OBREIRO,
        self::ROLE_DIACONO,
        self::ROLE_MEMBRO,
        self::ROLE_ANONYMOUS,
    ];


    public function sendEmailVerificationNotification()
    {

        $this->notify(new VerifyEmailNotification);
    }

    // 🔗 RELACIONAMENTOS
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function membros(): HasMany
    {
        return $this->hasMany(IgrejaMembro::class, 'user_id');
    }

    public function postReactions(): HasMany
    {
        return $this->hasMany(PostReaction::class, 'user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'user_id');
    }

    public function mensagensPrivadasEnviadas(): HasMany
    {
        return $this->hasMany(MensagemPrivada::class, 'remetente_id');
    }

    public function mensagensPrivadasRecebidas(): HasMany
    {
        return $this->hasMany(MensagemPrivada::class, 'destinatario_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class, 'user_id');
    }

    public function agenda(): HasMany
    {
        return $this->hasMany(Agenda::class, 'user_id');
    }

    public function engajamentoPontos(): HasMany
    {
        return $this->hasMany(EngajamentoPonto::class, 'user_id');
    }

    public function engajamentoBadges(): HasMany
    {
        return $this->hasMany(EngajamentoBadge::class, 'user_id');
    }

    // 🔗 RELACIONAMENTOS COM CURSOS
    public function cursosComoInstrutor(): HasMany
    {
        return $this->hasMany(\App\Models\Cursos\Curso::class, 'instrutor_principal');
    }

    public function cursosComoCoordenador(): HasMany
    {
        return $this->hasMany(\App\Models\Cursos\Curso::class, 'coordenador');
    }

    public function cursosCriados(): HasMany
    {
        return $this->hasMany(\App\Models\Cursos\Curso::class, 'created_by');
    }

    public function turmasComoInstrutor(): HasMany
    {
        return $this->hasMany(\App\Models\Cursos\CursoTurma::class, 'instrutor_id');
    }

    public function agendamentosRecursos(): HasMany
    {
        return $this->hasMany(AgendamentoRecurso::class, 'user_id');
    }

    public function doacoesOnline(): HasMany
    {
        return $this->hasMany(DoacaoOnline::class, 'user_id');
    }

    public function voluntario(): HasOne
    {
        return $this->hasOne(Voluntario::class, 'user_id');
    }

    public function atendimentosPastorais(): HasMany
    {
        return $this->hasMany(AtendimentoPastoral::class, 'pastor_id');
    }

    public function pedidosOracao(): HasMany
    {
        return $this->hasMany(PedidoOracao::class, 'user_id');
    }

    public function marketplacePedidos(): HasMany
    {
        return $this->hasMany(MarketplacePedido::class, 'comprador_id');
    }

    public function marketplacePagamentos(): HasMany
    {
        return $this->hasMany(MarketplacePagamento::class, 'user_id');
    }

    public function auditoriaLogs(): HasMany
    {
        return $this->hasMany(AuditoriaLog::class, 'usuario_id');
    }

    public function assinaturaLogs(): HasMany
    {
        return $this->hasMany(AssinaturaLog::class, 'usuario_id');
    }

    public function financeiroMovimentos(): HasMany
    {
        return $this->hasMany(FinanceiroMovimento::class, 'responsavel_id');
    }

    public function financeiroAuditoria(): HasMany
    {
        return $this->hasMany(FinanceiroAuditoria::class, 'alterado_por');
    }

    public function igrejaChatMensagens(): HasMany
    {
        return $this->hasMany(IgrejaChatMensagem::class, 'autor_id');
    }

    public function igrejaChats(): HasMany
    {
        return $this->hasMany(IgrejaChat::class, 'criado_por');
    }

    public function comunicacoes(): HasMany
    {
        return $this->hasMany(Comunicacao::class, 'enviado_por');
    }

    public function recursos(): HasMany
    {
        return $this->hasMany(Recurso::class, 'user_id');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'responsavel');
    }

    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class, 'membro_id');
    }

    public function escalasAuto(): HasMany
    {
        return $this->hasMany(EscalaAuto::class, 'voluntario_id');
    }

    public function ministerios(): HasMany
    {
        return $this->hasMany(Ministerio::class, 'user_id');
    }

    public function habilidades(): HasMany
    {
        return $this->hasMany(HabilidadesMembro::class, 'user_id');
    }

    public function membroPerfil(): HasOne
    {
        return $this->hasOne(MembroPerfil::class, 'user_id');
    }

    public function igrejaMembrosMinisterios(): HasMany
    {
        return $this->hasMany(IgrejaMembrosMinisterio::class, 'membro_id');
    }

    public function igrejaMembrosHistorico(): HasMany
    {
        return $this->hasMany(IgrejaMembrosHistorico::class, 'user_id');
    }

    public function enqueteDenuncias(): HasMany
    {
        return $this->hasMany(EnqueteDenuncia::class, 'criado_por');
    }

    public function relatoriosCache(): HasMany
    {
        return $this->hasMany(RelatorioCache::class, 'user_id');
    }

    public function assinaturaUpgrades(): HasMany
    {
        return $this->hasMany(AssinaturaUpgrade::class, 'usuario_id');
    }

    // ========================================
    // RELACIONAMENTO COM TRIAL
    // ========================================
    public function trial(): HasOne
    {
        return $this->hasOne(\App\Models\Billings\Trial\TrialUser::class, 'user_id');
    }

    /**
     * Verificar se o usuário tem acesso ativo ao trial
     * Método de acesso rápido para verificar trial ativo
     */
    public function hasTrialAccess(): bool
    {
        $trial = $this->trial()->first();

        if (!$trial) {
            return false;
        }

        return $trial->isAtivo();
    }

    /**
     * Obter informações detalhadas do trial do usuário
     */
    public function getTrialInfo(): ?array
    {
        $trial = $this->trial()->with('igreja')->first();

        if (!$trial) {
            return null;
        }

        return [
            'id' => $trial->id,
            'status' => $trial->status,
            'is_ativo' => $trial->isAtivo(),
            'esta_expirado' => $trial->estaExpirado(),
            'esta_em_graca' => $trial->estaEmPeriodoGraca(),
            'dias_restantes' => $trial->diasRestantes(),
            'dias_em_graca' => $trial->diasEmGraca(),
            'data_inicio' => $trial->data_inicio,
            'data_fim' => $trial->data_fim,
            'data_limite_graca' => $trial->data_limite_graca,
            'periodo_dias' => $trial->periodo_dias,
            'igreja' => $trial->igreja ? [
                'id' => $trial->igreja->id,
                'nome' => $trial->igreja->nome,
                'sigla' => $trial->igreja->sigla,
            ] : null,
            'estatisticas_uso' => $trial->getEstatisticasUso(),
            'pode_ser_reativado' => $trial->podeSerReativado(),
            'foi_reativado' => $trial->foiReativado(),
        ];
    }

    /**
     * Verificar se o trial do usuário está expirando em breve (7 dias ou menos)
     */
    public function trialEstaExpirando(): bool
    {
        $trial = $this->trial()->first();

        if (!$trial || !$trial->isAtivo()) {
            return false;
        }

        return $trial->diasRestantes() <= 7;
    }

    /**
     * Verificar se o trial do usuário está expirado
     */
    public function trialEstaExpirado(): bool
    {
        $trial = $this->trial()->first();

        if (!$trial) {
            return false;
        }

        return $trial->estaExpirado();
    }

    /**
     * Verificar se o trial do usuário está em período de graça
     */
    public function trialEstaEmGraca(): bool
    {
        $trial = $this->trial()->first();

        if (!$trial) {
            return false;
        }

        return $trial->estaEmPeriodoGraca();
    }

    public function relatoriosCultoCriados(): HasMany
    {
        return $this->hasMany(\App\Models\Igrejas\RelatorioCulto::class, 'created_by');
    }

    // ========================================
    // RELACIONAMENTOS DO SISTEMA DE SEGUIR
    // ========================================

    /**
     * Usuários que este usuário está seguindo (follower -> followed)
     */
    public function seguindo(): HasMany
    {
        return $this->hasMany(UserFollow::class, 'follower_id');
    }

    /**
     * Usuários que estão seguindo este usuário (followed -> followers)
     */
    public function seguidores(): HasMany
    {
        return $this->hasMany(UserFollow::class, 'followed_id');
    }

    /**
     * Atividades realizadas por este usuário
     */
    public function atividades(): HasMany
    {
        return $this->hasMany(UserFollowActivity::class, 'user_id');
    }

    /**
     * Notificações recebidas por este usuário
     */
    public function notificacoesSeguidores(): HasMany
    {
        return $this->hasMany(UserFollowNotification::class, 'follower_id');
    }

    /**
     * Notificações enviadas sobre atividades deste usuário
     */
    public function notificacoesEnviadas(): HasMany
    {
        return $this->hasMany(UserFollowNotification::class, 'followed_id');
    }

    // 🔗 RELACIONAMENTOS ESPECIAIS
    public function igrejasPrincipais(): HasMany
    {
        return $this->membros()->where('principal', true);
    }

    public function igrejasAtivas(): HasMany
    {
        return $this->membros()->where('status', 'ativo');
    }

    public function igrejasPastor(): HasMany
    {
        return $this->membros()->where('cargo', 'pastor');
    }

    public function igrejasAdmin(): HasMany
    {
        return $this->membros()->where('cargo', 'admin');
    }

    public function igrejasMinistro(): HasMany
    {
        return $this->membros()->where('cargo', 'ministro');
    }

    public function igrejasObreiro(): HasMany
    {
        return $this->membros()->where('cargo', 'obreiro');
    }

    public function igrejasDiacono(): HasMany
    {
        return $this->membros()->where('cargo', 'diacono');
    }

    public function igrejasMembro(): HasMany
    {
        return $this->membros()->where('cargo', 'membro');
    }


    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isRoot(): bool
    {
        return $this->role === self::ROLE_ROOT;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isIgrejaAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_PASTOR
        || $this->role === self::ROLE_MINISTRO;
    }

    public function isAnonymous(): bool
    {
        return $this->role === self::ROLE_ANONYMOUS;
    }

    public function isMembro(): bool
    {
        return $this->role === self::ROLE_MEMBRO;
    }

    public function isDiacono(): bool
    {
        return $this->role === self::ROLE_DIACONO;
    }

    public function isObreiro(): bool
    {
        return $this->role === self::ROLE_OBREIRO;
    }

    public function redirectDashboardRoute(): string
    {
        if (! $this->hasVerifiedEmail()) {
            return 'verification.notice'; // rota para verificar email
        }

        // Verificar se o usuário tem 2FA ativado E não passou pelo desafio
        if ($this->two_factor_secret && !session()->has('two-factor.login')) {
            return 'two-factor.login'; // rota de desafio 2FA
        }

        return match($this->role) {
            self::ROLE_SUPERADMIN => 'dashboard.administrative',
            self::ROLE_ADMIN => 'dashboard-admin.church',
            self::ROLE_PASTOR => 'dashboard-admin.church',
            self::ROLE_MINISTRO => 'dashboard-admin.church',
            self::ROLE_MEMBRO => 'dashboard.member',
            self::ROLE_DIACONO => 'dashboard.member',
            self::ROLE_OBREIRO => 'dashboard.member',
            self::ROLE_ROOT  => 'dashboard.root',
            default => 'dashboard',
        };
    }

    // Exemplo de checagem de múltiplos roles
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    // ========================================
    // Helpers para igreja do usuário
    // ========================================
    public function getIgrejaId()
    {
        // Primeiro verifica se há uma igreja selecionada na sessão
        $igrejaAtual = session('igreja_atual');
        if ($igrejaAtual) {
            return $igrejaAtual->id;
        }

        // Fallback: usa a primeira igreja ativa
        $membro = $this->igrejasAtivas()->first();
        return $membro ? $membro->igreja_id : null;
    }

    public function getIgreja()
    {
        // Primeiro verifica se há uma igreja selecionada na sessão
        $igrejaAtual = session('igreja_atual');
        if ($igrejaAtual) {
            return $igrejaAtual;
        }

        // Fallback: usa a primeira igreja ativa
        $membro = $this->igrejasAtivas()->first();
        return $membro ? $membro->igreja : null;
    }

    // ========================================
    // Helpers para roles e labels
    // ========================================
    public function getRoleLabel($role = null): string
    {
        $roleToCheck = $role ?? $this->role;

        return match($roleToCheck) {
            self::ROLE_ROOT => 'Super Administrador (Root)',
            self::ROLE_SUPERADMIN => 'Super Administrador',
            self::ROLE_ADMIN => 'Administrador da Igreja',
            self::ROLE_PASTOR => 'Pastor',
            self::ROLE_MINISTRO => 'Ministro',
            self::ROLE_OBREIRO => 'Obreiro',
            self::ROLE_DIACONO => 'Diácono',
            self::ROLE_MEMBRO => 'Membro',
            self::ROLE_ANONYMOUS => 'Anônimo',
            default => ucfirst($roleToCheck ?? 'Desconhecido')
        };
    }

    public function getRoleBadgeClass($role = null): string
    {
        $roleToCheck = $role ?? $this->role;

        return match($roleToCheck) {
            self::ROLE_ROOT => 'danger',
            self::ROLE_SUPERADMIN => 'danger',
            self::ROLE_ADMIN => 'warning',
            self::ROLE_PASTOR => 'primary',
            self::ROLE_MINISTRO => 'info',
            self::ROLE_OBREIRO => 'secondary',
            self::ROLE_DIACONO => 'success',
            self::ROLE_MEMBRO => 'light',
            self::ROLE_ANONYMOUS => 'dark',
            default => 'secondary'
        };
    }

    // ========================================
    // HELPERS DO SISTEMA DE SEGUIR
    // ========================================

    /**
     * Seguir um usuário
     */
    public function seguir(User $user): bool
    {
        return UserFollow::seguir($this->id, $user->id);
    }

    /**
     * Deixar de seguir um usuário
     */
    public function deixarSeguir(User $user): bool
    {
        return UserFollow::deixarSeguir($this->id, $user->id);
    }

    /**
     * Verificar se está seguindo um usuário
     */
    public function estaSeguindo(User $user): bool
    {
        return UserFollow::estaSeguindo($this->id, $user->id);
    }

    /**
     * Obter contagem de seguidores
     */
    public function getSeguidoresCount(): int
    {
        return UserFollow::contarSeguidores($this->id);
    }

    /**
     * Obter contagem de usuários seguidos
     */
    public function getSeguidosCount(): int
    {
        return UserFollow::contarSeguidos($this->id);
    }

    /**
     * Obter contagem de notificações não lidas
     */
    public function getNotificacoesNaoLidasCount(): int
    {
        return UserFollowNotification::contarNaoLidas($this->id);
    }

    /**
     * Marcar todas as notificações como lidas
     */
    public function marcarNotificacoesComoLidas(): int
    {
        return UserFollowNotification::marcarTodasComoLidas($this->id);
    }

    /**
     * Obter notificações recentes
     */
    public function getNotificacoesRecentes($limite = 20)
    {
        return UserFollowNotification::obterRecentes($this->id, $limite);
    }

    /**
     * Registrar uma atividade
     */
    public function registrarAtividade($activityType, $referenceId = null, $referenceType = null, $description = null, $metadata = [])
    {
        return UserFollowActivity::registrar(
            $this->id,
            $activityType,
            $referenceId,
            $referenceType,
            $description,
            $metadata
        );
    }

}
