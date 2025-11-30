<?php

namespace App\Models\Igrejas;



use App\Models\User;
use App\Models\Chats\Post;
use App\Models\Cursos\Curso;
use App\Traits\HasAuditoria;
use App\Models\Eventos\Escala;
use App\Models\Eventos\Evento;
use App\Models\Outros\Recurso;
use App\Models\Chats\Comentario;
use App\Models\Chats\IgrejaChat;
use App\Models\Chats\Comunicacao;
use App\Models\Chats\Notificacao;
use App\Models\Admin\AuditoriaLog;
use App\Models\Eventos\EscalaAuto;
use App\Models\Igrejas\Ministerio;
use App\Models\Igrejas\Voluntario;
use App\Models\Outros\DoacaoOnline;
use App\Models\Admin\RelatorioCache;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Pedidos\PedidoOracao;
use App\Models\Igrejas\AliancaIgreja;
use App\Models\Billings\AssinaturaLog;
use App\Models\Outros\EnqueteDenuncia;
use App\Models\Billings\IgrejaAssinada;
use App\Models\Igrejas\CategoriaIgreja;
use App\Models\Outros\EngajamentoBadge;
use App\Models\Outros\EngajamentoPonto;
use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaCiclo;
use App\Models\Billings\AssinaturaUpgrade;
use App\Models\Eventos\AgendamentoRecurso;
use App\Models\Financeiro\FinanceiroConta;
use App\Models\Billings\AssinaturaCupomUso;
use App\Models\Igrejas\AtendimentoPastoral;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Igrejas\IgrejaMetodoPagamento;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Billings\AssinaturaNotificacao;
use App\Models\Financeiro\FinanceiroAuditoria;
use App\Models\Financeiro\FinanceiroCategoria;
use App\Models\Financeiro\FinanceiroMovimento;
use App\Models\Igrejas\IgrejaMembrosMinisterio;
use App\Models\Billings\AssinaturaAutoRenovacao;
use App\Models\Billings\AssinaturaPagamentoFalha;
use App\Models\Financeiro\FinanceiroCanalDigital;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Igreja extends Model
{
    use HasFactory, SoftDeletes, HasAuditoria;

    protected $table = 'igrejas';
    protected $primaryKey = 'id';
    public $incrementing = true;   // BIGSERIAL
    protected $keyType = 'int';

    protected $fillable = [
        'nome',
        'nif',
        'sigla',
        'descricao',
        'sobre',
        'contacto',
        'localizacao',
        'logo',
        'status_aprovacao',
        'sede_id',
        'categoria_id',
        'tipo',
        'designacao',
        'created_by',
        'code_access',
    ];

    // 🔗 RELACIONAMENTOS
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaIgreja::class, 'categoria_id');
    }

    // Relacionamento muitos-para-muitos com alianças via tabela pivot
    public function aliancas()
    {
        return $this->belongsToMany(AliancaIgreja::class, 'igreja_aliancas', 'igreja_id', 'alianca_id')
                    ->withPivot(['status', 'data_adesao', 'data_desligamento', 'observacoes'])
                    ->withTimestamps();
    }

    // Alianças ativas (status = ativo)
    public function aliancasAtivas()
    {
        return $this->aliancas()->wherePivot('status', 'ativo');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'sede_id');
    }

    public function filiais(): HasMany
    {
        return $this->hasMany(Igreja::class, 'sede_id');
    }

    public function membros(): HasMany
    {
        return $this->hasMany(IgrejaMembro::class, 'igreja_id');
    }

    public function membrosMinisterios()
    {
        return $this->hasManyThrough(
            IgrejaMembrosMinisterio::class,
            IgrejaMembro::class,
            'igreja_id', // Foreign key on IgrejaMembro table
            'membro_id', // Foreign key on IgrejaMembroMinisterio table
            'id', // Local key on Igreja table
            'id' // Local key on IgrejaMembro table
        );
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assinaturaAtual(): HasOne
    {
        return $this->hasOne(AssinaturaAtual::class, 'igreja_id');
    }

    public function assinaturasHistorico(): HasMany
    {
        return $this->hasMany(AssinaturaHistorico::class, 'igreja_id');
    }

    public function assinaturasPagamentos(): HasMany
    {
        return $this->hasMany(AssinaturaPagamento::class, 'igreja_id');
    }

    public function assinaturasLogs(): HasMany
    {
        return $this->hasMany(AssinaturaLog::class, 'igreja_id');
    }

    public function assinaturasCiclos(): HasMany
    {
        return $this->hasMany(AssinaturaCiclo::class, 'igreja_id');
    }

    public function assinaturasPagamentosFalha(): HasMany
    {
        return $this->hasMany(AssinaturaPagamentoFalha::class, 'igreja_id');
    }

    public function assinaturaAutoRenovacao(): HasOne
    {
        return $this->hasOne(AssinaturaAutoRenovacao::class, 'igreja_id');
    }

    public function assinaturaCupomUsos(): HasMany
    {
        return $this->hasMany(AssinaturaCupomUso::class, 'igreja_id');
    }

    public function igrejaAssinada(): HasOne
    {
        return $this->hasOne(IgrejaAssinada::class, 'igreja_id');
    }

    public function igrejaMetodosPagamento(): HasMany
    {
        return $this->hasMany(IgrejaMetodoPagamento::class, 'igreja_id');
    }

    public function financeiroCategorias(): HasMany
    {
        return $this->hasMany(FinanceiroCategoria::class, 'igreja_id');
    }

    public function financeiroMovimentos(): HasMany
    {
        return $this->hasMany(FinanceiroMovimento::class, 'igreja_id');
    }

    public function financeiroContas(): HasMany
    {
        return $this->hasMany(FinanceiroConta::class, 'igreja_id');
    }

    public function financeiroCanaisDigitais(): HasMany
    {
        return $this->hasMany(FinanceiroCanalDigital::class, 'igreja_id');
    }

    public function financeiroAuditoria(): HasMany
    {
        return $this->hasMany(FinanceiroAuditoria::class, 'igreja_id');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'igreja_id');
    }


    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class, 'igreja_id');
    }

    public function escalasAuto(): HasMany
    {
        return $this->hasMany(EscalaAuto::class, 'igreja_id');
    }

    public function ministerios(): HasMany
    {
        return $this->hasMany(Ministerio::class, 'igreja_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'igreja_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'igreja_id');
    }

    public function comunicacoes(): HasMany
    {
        return $this->hasMany(Comunicacao::class, 'igreja_id');
    }

    public function igrejaChats(): HasMany
    {
        return $this->hasMany(IgrejaChat::class, 'igreja_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class, 'igreja_id');
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'igreja_id');
    }

    public function recursos(): HasMany
    {
        return $this->hasMany(Recurso::class, 'igreja_id');
    }

    public function agendamentosRecursos(): HasMany
    {
        return $this->hasMany(AgendamentoRecurso::class, 'igreja_id');
    }

    public function doacoesOnline(): HasMany
    {
        return $this->hasMany(DoacaoOnline::class, 'igreja_id');
    }


    public function engajamentoPontos(): HasMany
    {
        return $this->hasMany(EngajamentoPonto::class, 'igreja_id');
    }

    public function engajamentoBadges(): HasMany
    {
        return $this->hasMany(EngajamentoBadge::class, 'igreja_id');
    }

    public function atendimentosPastorais(): HasMany
    {
        return $this->hasMany(AtendimentoPastoral::class, 'igreja_id');
    }

    public function pedidosOracao(): HasMany
    {
        return $this->hasMany(PedidoOracao::class, 'igreja_id');
    }

    public function voluntarios(): HasMany
    {
        return $this->hasMany(Voluntario::class, 'igreja_id');
    }

    public function enqueteDenuncias(): HasMany
    {
        return $this->hasMany(EnqueteDenuncia::class, 'igreja_id');
    }

    public function auditoriaLogs(): HasMany
    {
        return $this->hasMany(AuditoriaLog::class, 'igreja_id');
    }

    public function relatoriosCache(): HasMany
    {
        return $this->hasMany(RelatorioCache::class, 'igreja_id');
    }

    public function assinaturaNotificacoes(): HasMany
    {
        return $this->hasMany(AssinaturaNotificacao::class, 'igreja_id');
    }

    public function assinaturaUpgrades(): HasMany
    {
        return $this->hasMany(AssinaturaUpgrade::class, 'igreja_id');
    }

    public function cartaoConfig(): HasOne
    {
        return $this->hasOne(\App\Models\CartaoConfig::class, 'igreja_id');
    }

    public function relatoriosCulto(): HasMany
    {
        return $this->hasMany(RelatorioCulto::class, 'igreja_id');
    }

    // Novos relacionamentos para controle SaaS
    public function consumoRecursos(): HasMany
    {
        return $this->hasMany(\App\Models\Billings\IgrejaConsumo::class, 'igreja_id');
    }

    public function recursosBloqueados(): HasMany
    {
        return $this->hasMany(\App\Models\IgrejaRecursosBloqueados::class, 'igreja_id');
    }

    public function assinaturaVerificacoes(): HasMany
    {
        return $this->hasMany(\App\Models\AssinaturaVerificacoes::class, 'igreja_id');
    }

    public function assinaturaAlertas(): HasMany
    {
        return $this->hasMany(\App\Models\AssinaturaAlertas::class, 'igreja_id');
    }

    public function pagamentosAssinaturaIgreja(): HasMany
    {
        return $this->hasMany(\App\Models\Billings\PagamentoAssinaturaIgreja::class, 'igreja_id');
    }

    // 🔗 RELACIONAMENTOS ESPECIAIS
    public function membrosAtivos(): HasMany
    {
        return $this->membros()->where('status', 'ativo');
    }

    public function membrosPastores(): HasMany
    {
        return $this->membros()->where('cargo', 'pastor');
    }

    public function membrosAdmins(): HasMany
    {
        return $this->membros()->where('cargo', 'admin');
    }

    public function lideranca(): HasMany
    {
        return $this->membros()->where('status', 'ativo')
                                ->whereIn('cargo', ['admin', 'pastor', 'ministro']);
    }

    public function assinaturaAtiva(): HasOne
    {
        return $this->assinaturaAtual()->where('status', 'Ativo');
    }

    public function assinaturaExpirada(): HasOne
    {
        return $this->assinaturaAtual()->where('status', 'Expirado');
    }

    public function assinaturaCancelada(): HasOne
    {
        return $this->assinaturaAtual()->where('status', 'Cancelado');
    }

    public function isSede(): bool
    {
        return $this->tipo === 'sede';
    }

    public function isFilial(): bool
    {
        return $this->tipo === 'filial';
    }

    public function isIndependente(): bool
    {
        return $this->tipo === 'independente';
    }

    public function isAprovada(): bool
    {
        return $this->status_aprovacao === 'aprovado';
    }

    public function isPendente(): bool
    {
        return $this->status_aprovacao === 'pendente';
    }

    public function isRejeitada(): bool
    {
        return $this->status_aprovacao === 'rejeitado';
    }

    // 🔗 MÉTODOS AUXILIARES PARA CONTROLE SAAS
    public function getConsumoAtual($recursoTipo)
    {
        return $this->consumoRecursos()
            ->where('recurso_tipo', $recursoTipo)
            ->where('periodo_referencia', now()->format('Y-m-01'))
            ->first();
    }

    public function getLimiteRecurso($recursoTipo)
    {
        return $this->assinaturaAtual?->getLimiteRecurso($recursoTipo);
    }

    public function podeUsarRecurso($recursoTipo, $quantidade = 1): bool
    {
        return $this->assinaturaAtual?->podeUsarRecurso($recursoTipo, $quantidade) ?? false;
    }

    public function consumirRecurso($recursoTipo, $quantidade = 1): bool
    {
        $consumo = $this->getConsumoAtual($recursoTipo);

        if (!$consumo) {
            // Criar novo registro de consumo
            $consumo = $this->consumoRecursos()->create([
                'recurso_tipo' => $recursoTipo,
                'consumo_atual' => 0,
                'limite_atual' => $this->getLimiteRecurso($recursoTipo),
                'periodo_referencia' => now()->format('Y-m-01'),
                'reset_automatico' => true,
            ]);
        }

        return $consumo->adicionarConsumo($quantidade);
    }

    public function temAssinaturaAtiva(): bool
    {
        return $this->assinaturaAtual?->estaAtiva() ?? false;
    }

    public function getStatusAssinatura(): array
    {
        return $this->assinaturaAtual?->getStatusDetalhado() ?? ['ativo' => false, 'status' => 'sem_assinatura'];
    }

    public function getUsoRecursos(): array
    {
        return $this->assinaturaAtual?->getUsoRecursos() ?? [];
    }

    public function temRecursoBloqueado($recursoTipo): bool
    {
        return $this->recursosBloqueados()
            ->where('recurso_tipo', $recursoTipo)
            ->where('ativo', true)
            ->exists();
    }

    public function getRecursosBloqueados(): array
    {
        return $this->recursosBloqueados()
            ->where('ativo', true)
            ->get()
            ->toArray();
    }

    public function getAlertasAtivos(): array
    {
        return $this->assinaturaAlertas()
            ->where('lido', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getVerificacoesRecentes($horas = 24): array
    {
        return $this->assinaturaVerificacoes()
            ->where('verificado_em', '>=', now()->subHours($horas))
            ->orderBy('verificado_em', 'desc')
            ->get()
            ->toArray();
    }

    public function registrarVerificacao($recurso, $acao, $status, $detalhes = [], $usuario = null): void
    {
        $this->assinaturaVerificacoes()->create([
            'recurso_solicitado' => $recurso,
            'acao_solicitada' => $acao,
            'status_verificacao' => $status,
            'detalhes' => $detalhes,
            'verificado_em' => now(),
            'usuario_id' => $usuario ? $usuario->id : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function criarAlerta($tipo, $titulo, $mensagem, $dados = [], $diasExpiracao = 7): void
    {
        $this->assinaturaAlertas()->create([
            'tipo_alerta' => $tipo,
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'dados' => $dados,
            'criado_em' => now(),
            'expires_at' => now()->addDays($diasExpiracao),
        ]);
    }

    public function bloquearRecurso($recursoTipo, $motivo, $usuarioId): void
    {
        $this->recursosBloqueados()->create([
            'recurso_tipo' => $recursoTipo,
            'motivo_bloqueio' => $motivo,
            'bloqueado_em' => now(),
            'bloqueado_por' => $usuarioId,
            'ativo' => true,
        ]);
    }

    public function desbloquearRecurso($recursoTipo, $usuarioId): void
    {
        $this->recursosBloqueados()
            ->where('recurso_tipo', $recursoTipo)
            ->where('ativo', true)
            ->update([
                'desbloqueado_em' => now(),
                'bloqueado_por' => $usuarioId,
                'ativo' => false,
            ]);
    }
}
