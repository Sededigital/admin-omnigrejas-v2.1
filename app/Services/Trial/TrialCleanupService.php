<?php

namespace App\Services\Trial;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Billings\Trial\TrialUser;
use App\Models\Billings\Trial\TrialDadosCriados;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TrialCleanupService
{
    /**
     * Limpeza completa de todos os dados de um trial expirado
     * Remove TODOS os dados criados pelo usuário durante o trial (HARD DELETE)
     * Mantém apenas: trial_users, trial_logs, trial_alertas, trial_requests
     * Remove permanentemente tudo, incluindo usuário e igreja
     */
    public static function limparTrialCompletamente(TrialUser $trial): void
    {
        DB::beginTransaction();
        try {
            $user = $trial->user;
            $igreja = $trial->igreja;

            Log::info('Iniciando limpeza completa do trial (HARD DELETE TOTAL)', [
                'trial_id' => $trial->id,
                'user_id' => $user->id,
                'igreja_id' => $igreja->id,
                'user_email' => $user->email,
            ]);

            // 1. Limpar dados criados durante o trial (hard delete)
            self::limparDadosCriadosDuranteTrial($trial);

            // 2. Limpar relacionamentos do usuário (hard delete)
            self::limparRelacionamentosUsuario($user);

            // 3. Limpar dados da igreja (hard delete)
            self::limparDadosIgreja($igreja);

            // 4. Limpar dados do usuário (hard delete)
            self::limparDadosUsuario($user);

            // 5. Hard delete da igreja
            $igreja->forceDelete(); // Hard delete completo

            // 6. Armazenar dados do usuário antes de deletar (para auditoria)
            $trial->update([
                'user_nome_deletado' => $user->name,
                'user_email_deletado' => $user->email,
                'user_telefone_deletado' => $user->phone,
                'deletado_em' => now(),
            ]);

            // 7. Hard delete do usuário (mantém registro em trial_users)
            $user->forceDelete(); // Hard delete completo

            // 7. Marcar dados criados como deletados permanentemente
            TrialDadosCriados::where('trial_user_id', $trial->id)
                ->where('soft_deleted', false)
                ->update([
                    'soft_deleted' => true,
                    'deleted_em' => now(),
                ]);

            DB::commit();

            Log::info('Limpeza completa do trial finalizada com sucesso (HARD DELETE TOTAL)', [
                'trial_id' => $trial->id,
                'user_email' => $user->email,
                'dados_usuario_preservados' => [
                    'nome' => $trial->user_nome_deletado,
                    'email' => $trial->user_email_deletado,
                    'telefone' => $trial->user_telefone_deletado,
                ],
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Erro na limpeza completa do trial', [
                'trial_id' => $trial->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Erro ao limpar trial completamente: ' . $e->getMessage());
        }
    }

    /**
     * Limpa dados criados durante o trial (HARD DELETE)
     */
    private static function limparDadosCriadosDuranteTrial(TrialUser $trial): void
    {
        $dadosCriados = TrialDadosCriados::where('trial_user_id', $trial->id)
            ->where('soft_deleted', false)
            ->get();

        foreach ($dadosCriados as $dado) {
            try {
                // Hard delete do registro
                self::deletarRegistroPorTabela($dado->tabela, $dado->registro_id);

                // Marcar como deletado permanentemente no rastreamento
                $dado->update([
                    'soft_deleted' => true,
                    'deleted_em' => now(),
                ]);

            } catch (Exception $e) {
                Log::warning('Erro ao deletar registro criado durante trial', [
                    'trial_id' => $trial->id,
                    'tabela' => $dado->tabela,
                    'registro_id' => $dado->registro_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Limpa relacionamentos do usuário (HARD DELETE)
     */
    private static function limparRelacionamentosUsuario(User $user): void
    {
        // Posts e comentários - HARD DELETE
        $user->posts()->delete();
        $user->comentarios()->delete();
        $user->postReactions()->delete();

        // Mensagens privadas - HARD DELETE
        $user->mensagensPrivadasEnviadas()->delete();
        $user->mensagensPrivadasRecebidas()->delete();

        // Notificações - HARD DELETE
        $user->notificacoes()->delete();

        // Engajamento - HARD DELETE
        $user->engajamentoPontos()->delete();
        $user->engajamentoBadges()->delete();

        // Cursos - HARD DELETE
        $user->cursosComoInstrutor()->delete();
        $user->cursosComoCoordenador()->delete();
        $user->cursosCriados()->delete();
        $user->turmasComoInstrutor()->delete();

        // Recursos e agendamentos (da igreja, não do usuário) - HARD DELETE
        $igreja = $user->getIgreja();
        if ($igreja) {
            $igreja->recursos()->delete();
            $igreja->agendamentosRecursos()->delete();

            // Doações - HARD DELETE
            $igreja->doacoesOnline()->delete();

            // Atendimentos pastorais - HARD DELETE
            $igreja->atendimentosPastorais()->delete();

            // Pedidos de oração - HARD DELETE
            $igreja->pedidosOracao()->delete();

            $igreja->assinaturasLogs()->delete();

            // Financeiro - HARD DELETE
            $igreja->financeiroMovimentos()->delete();
            // $igreja->financeiroAuditoria()->delete(); // Removido - tabela não tem igreja_id

            // Chats - HARD DELETE
            // $igreja->igrejaChatMensagens()->delete(); // Removido - método não existe
            $igreja->igrejaChats()->delete();
            $igreja->comunicacoes()->delete();

            // Eventos - HARD DELETE
            $igreja->eventos()->delete();
            // $igreja->escalas()->delete(); // Removido - tabela não tem igreja_id
            $igreja->escalasAuto()->delete();

            // Enquetes - HARD DELETE
            $igreja->enqueteDenuncias()->delete();

            // Relatórios - HARD DELETE
            $igreja->relatoriosCache()->delete();
        }

        // Marketplace (do usuário) - HARD DELETE
        $user->marketplacePedidos()->delete();

        $user->igrejaMembrosMinisterios()->delete();

        // Ministérios membros - HARD DELETE
        $user->igrejaMembrosMinisterios()->delete();

        // Sistema de seguir - HARD DELETE
        $user->seguindo()->delete();
        $user->seguidores()->delete();
        $user->atividades()->delete();
        $user->notificacoesSeguidores()->delete();
        $user->notificacoesEnviadas()->delete();

        // Assinatura upgrades (do usuário) - HARD DELETE
        $user->assinaturaUpgrades()->delete();

        // Relatórios cultos - HARD DELETE
        $user->relatoriosCultoCriados()->delete();
    }

    /**
     * Limpa dados específicos da igreja (HARD DELETE)
     */
    private static function limparDadosIgreja(Igreja $igreja): void
    {
        // Membros da igreja (exceto o admin que será deletado junto com o usuário) - HARD DELETE
        $igreja->membros()->where('cargo', '!=', 'admin')->delete();

        // Posts da igreja - HARD DELETE
        $igreja->posts()->delete();

        // Eventos da igreja - HARD DELETE
        $igreja->eventos()->delete();

        // Ministérios da igreja - deletar ministérios diretamente - HARD DELETE
        $igreja->ministerios()->delete();

        // Recursos da igreja - HARD DELETE
        $igreja->recursos()->delete();


        // Atendimentos pastorais - HARD DELETE
        $igreja->atendimentosPastorais()->delete();

        // Doações online - HARD DELETE
        $igreja->doacoesOnline()->delete();

        // Relatórios - HARD DELETE
        $igreja->relatoriosCulto()->delete();

        // Enquetes - HARD DELETE
        // $igreja->enquetes()->delete(); // Removido - método não existe na model Igreja

        // Configurações - HARD DELETE
        $igreja->cartaoConfig()->delete();
    }

    /**
     * Limpa dados específicos do usuário (HARD DELETE)
     */
    private static function limparDadosUsuario(User $user): void
    {
        // Agenda - HARD DELETE
        $user->agenda()->delete();
    }

    /**
     * Deleta registro específico por tabela (HARD DELETE)
     */
    private static function deletarRegistroPorTabela(string $tabela, string $registroId): void
    {
        try {
            switch ($tabela) {
                case 'users':
                    User::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'igrejas':
                    Igreja::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'igreja_membros':
                    IgrejaMembro::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'posts':
                    \App\Models\Chats\Post::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'comentarios':
                    \App\Models\Chats\Comentario::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'eventos':
                    \App\Models\Eventos\Evento::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'ministerios':
                    \App\Models\Igrejas\Ministerio::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'cursos':
                    \App\Models\Cursos\Curso::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'curso_turmas':
                    \App\Models\Cursos\CursoTurma::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'curso_matriculas':
                    \App\Models\Cursos\CursoMatricula::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'marketplace_produtos':
                    \App\Models\Marketplace\MarketplaceProduto::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'marketplace_pedidos':
                    \App\Models\Marketplace\MarketplacePedido::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'doacoes_online':
                    \App\Models\Outros\DoacaoOnline::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'voluntarios':
                    \App\Models\Igrejas\Voluntario::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'atendimentos_pastorais':
                    \App\Models\Igrejas\AtendimentoPastoral::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'pedidos_especiais':
                    \App\Models\Pedidos\PedidoOracao::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'recursos':
                    \App\Models\Outros\Recurso::where('id', $registroId)->delete(); // Hard delete
                    break;

                case 'agendamentos':
                    \App\Models\Eventos\AgendamentoRecurso::where('id', $registroId)->delete(); // Hard delete
                    break;

                default:
                    Log::warning('Tabela não mapeada para limpeza', [
                        'tabela' => $tabela,
                        'registro_id' => $registroId,
                    ]);
                    break;
            }
        } catch (Exception $e) {
            Log::error('Erro ao deletar registro específico', [
                'tabela' => $tabela,
                'registro_id' => $registroId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Lista todas as tabelas que serão afetadas na limpeza (HARD DELETE)
     */
    public static function getTabelasAfetadas(): array
    {
        return [
            // Usuário e igreja principal - HARD DELETE
            'users',
            'igrejas',
            'igreja_membros',
            'membro_perfis',
            'igreja_membros_historico',

            // Posts e interações - HARD DELETE
            'posts',
            'comentarios',
            'post_reactions',

            // Mensagens - HARD DELETE
            'mensagens_privadas',
            'notificacoes',

            // Engajamento - HARD DELETE
            'engajamento_pontos',
            'engajamento_badges',

            // Cursos - HARD DELETE
            'cursos',
            'curso_turmas',
            'curso_matriculas',

            // Recursos e agendamentos - HARD DELETE
            'recursos',
            'agendamento_recursos',

            // Financeiro - HARD DELETE
            'doacoes_online',
            'financeiro_movimentos',
            'financeiro_auditoria',

            // Voluntariado - HARD DELETE
            'voluntarios',

            // Atendimentos - HARD DELETE
            'atendimentos_pastorais',
            'pedidos_oracao',

            // Marketplace - HARD DELETE
            'marketplace_produtos',
            'marketplace_pedidos',
            'marketplace_pagamentos',

            // Chats e comunicação - HARD DELETE
            'igreja_chats',
            'igreja_chat_mensagens',
            'comunicacoes',

            // Eventos - HARD DELETE
            'eventos',
            'escalas',
            'escala_auto',

            // Ministérios - HARD DELETE
            'ministerios', // Agora deletamos ministérios diretamente
            'habilidades_membro',
            'igreja_membros_ministerios',

            // Sistema de seguir - HARD DELETE
            'user_follows',
            'user_follow_activities',
            'user_follow_notifications',

            // Relatórios - HARD DELETE
            'relatorios_cache',
            'relatorios_culto',
            'assinatura_upgrades',

            // Auditoria - HARD DELETE
            'auditoria_logs',
            'assinatura_logs',

            // Enquetes - HARD DELETE
            'enquetes',
            'enquete_denuncias',

            // Configurações - HARD DELETE
            'cartao_config',
        ];
    }

    /**
     * Simula a limpeza sem executar (para debug) - HARD DELETE TOTAL
     */
    public static function simularLimpeza(TrialUser $trial): array
    {
        $user = $trial->user;
        $igreja = $trial->igreja;

        $simulacao = [
            'trial' => [
                'id' => $trial->id,
                'user_email' => $user->email,
                'igreja_nome' => $igreja->nome,
            ],
            'dados_criados' => TrialDadosCriados::where('trial_user_id', $trial->id)->count(),
            'tabelas_afetadas' => self::getTabelasAfetadas(),
            'relacionamentos_usuario' => [
                'posts' => $user->posts()->count(),
                'comentarios' => $user->comentarios()->count(),
                'eventos' => $user->eventos()->count(),
                'cursos' => $user->cursosCriados()->count(),
                'doacoes' => $user->doacoesOnline()->count(),
                'marketplace' => $user->marketplacePedidos()->count(),
            ],
            'dados_igreja' => [
                'membros' => $igreja->membros()->count(),
                'posts' => $igreja->posts()->count(),
                'eventos' => $igreja->eventos()->count(),
                'ministerios' => $igreja->ministerios()->count(),
                'recursos' => $igreja->recursos()->count(),
            ],
            'tipo_limpeza' => 'HARD DELETE TOTAL (com preservação de dados do usuário)',
            'aviso' => 'Esta operação irá REMOVER PERMANENTEMENTE TUDO, incluindo usuário e igreja. Os dados básicos do usuário serão preservados na tabela trial_users para auditoria.',
            'itens_deletados' => [
                'usuario' => 'Sim - Hard Delete (dados preservados em trial_users)',
                'igreja' => 'Sim - Hard Delete',
                'todos_relacionamentos' => 'Sim - Hard Delete',
                'todos_dados_criados' => 'Sim - Hard Delete',
            ],
            'dados_preservados' => [
                'trial_users' => 'Nome, email, telefone do usuário deletado',
                'trial_logs' => 'Histórico completo de ações',
                'trial_alertas' => 'Alertas enviados',
                'trial_requests' => 'Solicitações originais',
                'trial_dados_criados' => 'Rastreamento de dados criados (soft deleted)',
            ],
        ];

        return $simulacao;
    }
}