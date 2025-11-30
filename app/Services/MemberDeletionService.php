<?php

namespace App\Services;

use App\Models\User;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Serviço responsável por gerenciar exclusões de membros
 * Centraliza toda lógica de exclusão para manter consistência e reutilização
 */
class MemberDeletionService
{
    /**
     * Exclui completamente um membro do sistema (hard delete)
     * Remove fisicamente todos os registros relacionados ao usuário
     *
     * @param User $user Usuário a ser excluído
     * @param IgrejaMembro $member Membro relacionado
     * @param User $deletedBy Usuário que está executando a exclusão
     * @return bool
     * @throws \Exception
     */
    public function deleteMemberCompletely(User $user, IgrejaMembro $member, User $deletedBy): bool
    {
        DB::beginTransaction();

        try {
            // Registrar em auditoria_logs ANTES da exclusão
            $this->logDeletion('users', $user->id, [
                'motivo' => 'Exclusão completa do membro - conta removida permanentemente do sistema',
                'membro_id' => $member->id,
                'igreja_id' => $member->igreja_id,
                'nome' => $user->name,
                'email' => $user->email,
                'excluido_por' => $deletedBy->name,
            ], $deletedBy->id);

            // Exclusão física permanente de todos os relacionamentos
            $this->deleteUserRelationships($user);

            // Por fim, excluir o usuário fisicamente
            $user->forceDelete();

            DB::commit();

            Log::info('Membro excluído completamente do sistema', [
                'user_id' => $user->id,
                'member_id' => $member->id,
                'deleted_by' => $deletedBy->id,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro na exclusão completa do membro', [
                'user_id' => $user->id,
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Remove membro apenas da igreja (soft delete)
     * Mantém o usuário no sistema mas muda seu role para anonymous
     *
     * @param IgrejaMembro $member Membro a ser removido
     * @param User $deletedBy Usuário que está executando a remoção
     * @return bool
     * @throws \Exception
     */
    public function removeMemberFromChurch(IgrejaMembro $member, User $deletedBy): bool
    {
        DB::beginTransaction();

        try {
            $user = $member->user;

            // Registrar em auditoria_logs
            $this->logDeletion('igreja_membros', $member->id, [
                'motivo' => 'Remoção da igreja - usuário permanece no sistema como anonymous',
                'membro_id' => $member->id,
                'igreja_id' => $member->igreja_id,
                'nome' => $user->name,
                'email' => $user->email,
                'excluido_por' => $deletedBy->name,
            ], $deletedBy->id);

            // Apenas remover da igreja (soft delete do membro)
            $member->delete();

            // Mudar role do usuário para anonymous
            $user->update(['role' => 'anonymous']);

            DB::commit();

            Log::info('Membro removido da igreja', [
                'member_id' => $member->id,
                'user_id' => $user->id,
                'church_id' => $member->igreja_id,
                'deleted_by' => $deletedBy->id,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro na remoção do membro da igreja', [
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Exclui fisicamente todos os relacionamentos de um usuário
     *
     * @param User $user
     * @return void
     */
    private function deleteUserRelationships(User $user): void
    {
        // Relacionamentos principais que existem no modelo User
        $user->membros()->forceDelete(); // igreja_membros
        $user->postReactions()->forceDelete();
        $user->posts()->forceDelete();
        $user->comentarios()->forceDelete();
        $user->mensagensPrivadasEnviadas()->forceDelete();
        $user->mensagensPrivadasRecebidas()->forceDelete();
        $user->notificacoes()->forceDelete();
        $user->agenda()->forceDelete();
        $user->engajamentoPontos()->forceDelete();
        $user->engajamentoBadges()->forceDelete();

        // Relacionamentos pastorais e espirituais (queries diretas)
        DB::table('atendimentos_pastorais')->where('pastor_id', $user->id)->delete();
        DB::table('pedidos_oracao')->where('membro_id', $user->id)->delete(); // Corrigido: usa membro_id

        // Relacionamentos de marketplace (queries diretas)
        DB::table('marketplace_pedidos')->where('comprador_id', $user->id)->delete();
        DB::table('marketplace_pagamentos')->whereIn('pedido_id',
            DB::table('marketplace_pedidos')->where('comprador_id', $user->id)->pluck('id')
        )->delete();

        // Logs e auditoria
        $user->auditoriaLogs()->forceDelete();
        $user->assinaturaLogs()->forceDelete();

        // Relacionamentos financeiros
        $user->financeiroMovimentos()->forceDelete();
        $user->financeiroAuditoria()->forceDelete();

        // Relacionamentos de chat e comunicação
        $user->igrejaChatMensagens()->forceDelete();
        $user->igrejaChats()->forceDelete();
        $user->comunicacoes()->forceDelete();

        // Relacionamentos de recursos e eventos
        // Nota: tabela 'recursos' não tem coluna 'created_by'
        DB::table('eventos')->where('created_by', $user->id)->delete();
        DB::table('escalas')->where('membro_id', $user->id)->delete();

        // Excluir escalas automáticas dos voluntários deste membro
        $voluntarioIds = DB::table('voluntarios')->where('membro_id', $user->id)->pluck('id');
        if ($voluntarioIds->isNotEmpty()) {
            DB::table('escala_auto')->whereIn('voluntario_id', $voluntarioIds)->delete();
        }

        // Relacionamentos ministeriais (queries diretas)
        // Nota: tabela 'ministerios' não tem coluna 'created_by'
        DB::table('habilidades_membros')->where('membro_id', $user->id)->delete();
        DB::table('membro_perfis')->where('created_by', $user->id)->delete();
        DB::table('igreja_membros_ministerios')->where('membro_id', $user->id)->delete();
        DB::table('igreja_membros_historico')->where('igreja_membro_id', $user->id)->delete();

        // Outros relacionamentos (queries diretas)
        DB::table('enquete_denuncias')->where('criado_por', $user->id)->delete();
        // Nota: tabela 'relatorios_cache' não tem coluna 'created_by'
        DB::table('relatorio_culto')->where('created_by', $user->id)->delete();

        // Relacionamentos do sistema de seguir (queries diretas)
        DB::table('user_follows')->where('follower_id', $user->id)->orWhere('followed_id', $user->id)->delete();
        DB::table('user_follow_activities')->where('user_id', $user->id)->delete();
        DB::table('user_follow_notifications')->where('follower_id', $user->id)->orWhere('followed_id', $user->id)->delete();

        // Voluntários (relacionamento indireto)
        DB::table('voluntarios')->where('membro_id', $user->id)->delete();
    }

    /**
     * Registra uma ação de exclusão nos logs de auditoria
     *
     * @param string $table Nome da tabela afetada
     * @param string $recordId ID do registro afetado
     * @param array $details Detalhes da exclusão
     * @param string $userId ID do usuário que executou a ação
     * @return void
     */
    private function logDeletion(string $table, string $recordId, array $details, string $userId): void
    {
        DB::table('auditoria_logs')->insert([
            'id' => (string) Str::uuid(),
            'tabela' => $table,
            'registro_id' => $recordId,
            'acao' => 'delete',
            'usuario_id' => $userId,
            'data_acao' => now(),
            'valores' => json_encode($details),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Verifica se um membro foi criado por outro usuário da igreja
     *
     * @param IgrejaMembro $member
     * @return bool
     */
    public function wasCreatedByChurchMember(IgrejaMembro $member): bool
    {
        return $member->created_by && $member->created_by !== $member->user_id;
    }

    /**
     * Determina o tipo de exclusão baseado na origem do membro
     *
     * @param IgrejaMembro $member
     * @return string 'complete' ou 'church_only'
     */
    public function getDeletionType(IgrejaMembro $member): string
    {
        return $this->wasCreatedByChurchMember($member) ? 'complete' : 'church_only';
    }

    /**
     * Verifica se um usuário tem permissão para excluir um membro específico
     *
     * Regras de permissão:
     * - Admin, Pastor, Ministro: podem excluir QUALQUER membro
     * - Diácono: só pode excluir membros (cargo = 'membro')
     * - Obreiro: só pode excluir membros (cargo = 'membro')
     *
     * @param User $user Usuário que está tentando excluir
     * @param IgrejaMembro $memberToDelete Membro a ser excluído
     * @return bool
     */
    public function canDeleteMember(User $user, IgrejaMembro $memberToDelete): bool
    {
        // Obter o cargo do usuário na igreja
        $userMember = $user->membros()
            ->where('igreja_id', $memberToDelete->igreja_id)
            ->where('status', 'ativo')
            ->first();

        // Se o usuário não é membro ativo desta igreja, não pode excluir
        if (!$userMember) {
            return false;
        }

        $userRole = $userMember->cargo;

        // Admin, Pastor, Ministro podem excluir qualquer membro
        if (in_array($userRole, ['admin', 'pastor', 'ministro'])) {
            return true;
        }

        // Diácono e Obreiro só podem excluir membros comuns
        if (in_array($userRole, ['diacono', 'obreiro'])) {
            return $memberToDelete->cargo === 'membro';
        }

        // Outros cargos não têm permissão
        return false;
    }

    /**
     * Valida se o usuário pode excluir o membro e lança exceção se não puder
     *
     * @param User $user Usuário que está tentando excluir
     * @param IgrejaMembro $memberToDelete Membro a ser excluído
     * @throws \Exception
     */
    public function validateDeletionPermission(User $user, IgrejaMembro $memberToDelete): void
    {
        if (!$this->canDeleteMember($user, $memberToDelete)) {
            
            $userMember = $user->membros()
                ->where('igreja_id', $memberToDelete->igreja_id)
                ->where('status', 'ativo')
                ->first();

            $userRole = $userMember ? $userMember->cargo : 'desconhecido';

            throw new \Exception(
                "Usuário com cargo '{$userRole}' não tem permissão para excluir membro com cargo '{$memberToDelete->cargo}'"
            );
        }
    }
}
