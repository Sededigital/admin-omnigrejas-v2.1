<?php

namespace App\Services\Trial;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Billings\Trial\TrialUser;
use App\Models\Billings\Trial\TrialLog;
use App\Models\Billings\Trial\TrialDadosCriados;
use App\Models\Igrejas\CategoriaIgreja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class TrialService
{
    /**
     * Criar um novo usuário trial
     */
    public static function criarTrial(array $dados): TrialUser
    {
        DB::beginTransaction();

        try {
            // Validar dados obrigatórios
            self::validarDadosCriacao($dados);
            $nome_categoria = $dados['denominacao'];


            // Criar usuário
            $user = User::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => $dados['name'],
                'email' => $dados['email'],
                'email_verified_at'=> now()->format('Y-m-d H:i:s'),
                'password' => Hash::make($dados['password']),
                'denominacao'=>$nome_categoria,
                'role' => 'admin',
                'is_active' => true,
                'status' => 'ativo',
            ]);

            # => Traz a categoria
            

            if($nome_categoria){                
            
                $categoria = CategoriaIgreja::where('nome', $nome_categoria)->first();
            
            }

            // Criar igreja
            $igreja = Igreja::create([
                'nome' => $dados['igreja_nome'] ?? $dados['name'] . ' - Trial',
                'nif' => 'TRIAL-' . strtoupper(substr(md5($user->id), 0, 8)),
                'sigla' => 'TRIAL',
                'status_aprovacao' => 'aprovado',
                'localizacao'=> $dados['provincia'] .', ' .$dados['cidade'] ?? NULL,
                'categoria_id' => $categoria->id ?? NULL, 
                'created_by' => $user->id,
            ]);

            // Criar membro admin
            $membro = IgrejaMembro::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'igreja_id' => $igreja->id,
                'user_id' => $user->id,
                'cargo' => 'admin',
                'principal' => true,
                'created_by' => $user->id,
            ]);

            // Criar perfil do membro
            \App\Models\Igrejas\MembroPerfil::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'igreja_membro_id' => $membro->id,
                'genero' => 'masculino', // padrão
                'data_nascimento' => now()->subYears(30), // data padrão
                'endereco' => $dados['cidade'] ?? 'Endereço não informado',
                'observacoes' => 'Membro criado automaticamente para o período de teste',
                'created_by' => $user->id,
            ]);

            // Criar histórico do membro
            \App\Models\Igrejas\IgrejaMembrosHistorico::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'igreja_membro_id' => $membro->id,
                'cargo' => 'admin',
                'inicio' => now(),
                'fim' => null, // ainda ativo
            ]);

            // Calcular datas
            $periodoDias = $dados['periodo_dias'] ?? 10;
            $dataInicio = now();
            $dataFim = $dataInicio->copy()->addDays($periodoDias);
            $dataLimiteGraca = $dataFim->copy()->addDays(7);

            // Criar trial
            $trial = TrialUser::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'igreja_id' => $igreja->id,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'periodo_dias' => $periodoDias,
                'data_limite_graca' => $dataLimiteGraca,
                'periodo_graca_dias' => 30,
                'criado_por' => $dados['criado_por'] ?? null,
            ]);

            // Log da criação
            TrialLog::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'trial_user_id' => $trial->id,
                'acao' => 'criado',
                'descricao' => 'Trial criado para usuário ' . $user->email,
                'dados' => [
                    'user_email' => $user->email,
                    'igreja_nome' => $igreja->nome,
                    'periodo_dias' => $periodoDias,
                ],
                'realizado_por' => $dados['criado_por'] ?? null,
                'realizado_em' => now(),
            ]);

            DB::commit();
            return $trial;

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Erro ao criar trial: ' . $e->getMessage());
        }
    }

    /**
     * Verificar status do trial de um usuário
     */
    public static function verificarStatusTrial(User $user): array
    {
        $trial = $user->trial;

        if (!$trial) {
            return [
                'status' => 'nao_trial',
                'ativo' => false,
                'mensagem' => 'Usuário não possui trial ativo'
            ];
        }

        if ($trial->isAtivo()) {
            return [
                'status' => 'ativo',
                'ativo' => true,
                'dias_restantes' => $trial->diasRestantes(),
                'expira_em' => $trial->getDataFimFormatada(),
                'trial' => $trial,
            ];
        }

        if ($trial->estaEmPeriodoGraca()) {
            return [
                'status' => 'periodo_graca',
                'ativo' => false,
                'dias_graca' => $trial->diasEmGraca(),
                'pode_reativar' => $trial->podeSerReativado(),
                'trial' => $trial,
            ];
        }

        return [
            'status' => 'expirado',
            'ativo' => false,
            'expirou_em' => $trial->getDataFimFormatada(),
            'trial' => $trial,
        ];
    }

    /**
     * Reativar um trial expirado
     */
    public static function reativarTrial(TrialUser $trial, User $admin, int $diasExtensao = 7): bool
    {
        if (!$trial->podeSerReativado()) {
            throw new Exception('Trial não pode ser reativado');
        }

        DB::beginTransaction();
        try {
            $sucesso = $trial->reativar($admin, $diasExtensao);

            if ($sucesso) {
                TrialLog::logReativacaoTrial($trial, $admin);
            }

            DB::commit();
            return $sucesso;

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Erro ao reativar trial: ' . $e->getMessage());
        }
    }

    /**
     * Bloquear um trial
     */
    public static function bloquearTrial(TrialUser $trial, User $admin, string $motivo = null): void
    {
        DB::beginTransaction();
        try {
            $trial->bloquear($motivo);
            TrialLog::logBloqueioTrial($trial, $admin, $motivo);

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Erro ao bloquear trial: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar um trial
     */
    public static function cancelarTrial(TrialUser $trial, User $admin, string $motivo = null): void
    {
        DB::beginTransaction();
        try {
            $trial->cancelar($motivo);
            TrialLog::logCancelamentoTrial($trial, $admin, $motivo);

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Erro ao cancelar trial: ' . $e->getMessage());
        }
    }

    /**
     * Registrar acesso ao trial
     */
    public static function registrarAcessoTrial(User $user): void
    {
        if ($user->role === 'admin' && $user->trial) {
            $user->trial->atualizarUltimoAcesso();
            TrialLog::logAcessoTrial($user->trial);
        }
    }

    /**
     * Registrar dado criado durante trial
     */
    public static function registrarDadoCriado(User $user, string $tabela, string $registroId, string $tipoDado): void
    {
        if ($user->role === 'admin' && $user->trial) {
            TrialDadosCriados::create([
                'trial_user_id' => $user->trial->id,
                'tabela' => $tabela,
                'registro_id' => $registroId,
                'tipo_dado' => $tipoDado,
            ]);

            TrialLog::logDadosCriados($user->trial, $tabela, $registroId, $tipoDado);
        }
    }

    /**
     * Obter estatísticas dos trials
     */
    public static function getEstatisticas(): array
    {
        $trials = TrialUser::all();

        return [
            'total_trials' => $trials->count(),
            'trials_ativos' => $trials->where('status', 'ativo')->count(),
            'trials_expirados' => $trials->where('status', 'expirado')->count(),
            'trials_em_graca' => $trials->filter(fn($t) => $t->estaEmPeriodoGraca())->count(),
            'trials_bloqueados' => $trials->where('status', 'bloqueado')->count(),
            'trials_cancelados' => $trials->where('status', 'cancelado')->count(),
            'media_membros_criados' => $trials->avg('total_membros_criados'),
            'media_posts_criados' => $trials->avg('total_posts_criados'),
            'media_eventos_criados' => $trials->avg('total_eventos_criados'),
            'total_membros_criados' => $trials->sum('total_membros_criados'),
            'total_posts_criados' => $trials->sum('total_posts_criados'),
            'total_eventos_criados' => $trials->sum('total_eventos_criados'),
        ];
    }

    /**
     * Obter trials que precisam de notificação
     */
    public static function getTrialsParaNotificar(): array
    {
        $trials7Dias = TrialUser::where('status', 'ativo')
            ->where('data_fim', now()->addDays(7))
            ->get();

        $trials1Dia = TrialUser::where('status', 'ativo')
            ->where('data_fim', now()->addDay())
            ->get();

        return [
            '7_dias' => $trials7Dias,
            '1_dia' => $trials1Dia,
        ];
    }

    /**
     * Obter trials que devem expirar
     */
    public static function getTrialsParaExpirar(): \Illuminate\Database\Eloquent\Collection
    {
        return TrialUser::where('status', 'ativo')
            ->where('data_fim', '<=', now())
            ->get();
    }

    /**
     * Obter trials que devem ser limpos (expirados há mais de 3 dias)
     */
    public static function getTrialsParaLimpar(): \Illuminate\Database\Eloquent\Collection
    {
        return TrialUser::where('status', 'expirado')
            ->where('data_fim', '<=', now()->subDays(3))
            ->get();
    }

    /**
     * Limpar dados de um trial expirado (versão antiga - mantida para compatibilidade)
     * @deprecated Use TrialCleanupService::limparTrialCompletamente() para limpeza completa
     */
    public static function limparTrialExpirado(TrialUser $trial): void
    {
        // Redirecionar para o novo serviço de limpeza
        TrialCleanupService::limparTrialCompletamente($trial);
    }

    /**
     * Validar dados para criação de trial
     */
    private static function validarDadosCriacao(array $dados): void
    {
        if (empty($dados['name'])) {
            throw new Exception('Nome é obrigatório');
        }

        if (empty($dados['email'])) {
            throw new Exception('Email é obrigatório');
        }

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }

        if (empty($dados['password'])) {
            throw new Exception('Senha é obrigatória');
        }

        if (strlen($dados['password']) < 6) {
            throw new Exception('Senha deve ter pelo menos 6 caracteres');
        }

        // Verificar se email já existe
        if (User::where('email', $dados['email'])->exists()) {
            throw new Exception('Email já cadastrado');
        }
    }

    /**
     * Verificar se usuário pode acessar recurso (usado em middlewares)
     */
    public static function usuarioPodeAcessar(User $user): bool
    {
        // Se não é admin, pode acessar normalmente
        if ($user->role !== 'admin') {
            return true;
        }

        // Se é admin mas não tem trial, pode acessar (assinatura paga)
        if (!$user->trial) {
            return true;
        }

        // Verificar status do trial
        $status = self::verificarStatusTrial($user);
        return $status['ativo'];
    }

    /**
     * Obter mensagem de bloqueio para usuário trial
     */
    public static function getMensagemBloqueio(User $user): string
    {
        $status = self::verificarStatusTrial($user);

        return match($status['status']) {
            'periodo_graca' => "Seu período de teste expirou. Você tem {$status['dias_graca']} dias para reativar sua conta.",
            'expirado' => 'Seu período de teste expirou. Entre em contato para continuar usando.',
            default => 'Acesso temporariamente indisponível.',
        };
    }
}