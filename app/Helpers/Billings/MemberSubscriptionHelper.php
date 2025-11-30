<?php

namespace App\Helpers\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Log;

class MemberSubscriptionHelper
{
    /**
     * Verificar se igreja pode adicionar membros
     */
    public static function canAddMember(int $igrejaId, int $quantidade = 1): array
    {
        return SubscriptionHelper::canConsumeResource($igrejaId, 'membros', $quantidade);
    }

    /**
     * Registrar adição de membro
     */
    public static function registerMemberAdded(int $igrejaId, int $memberId, array $detalhes = []): bool
    {
        // Verificar se pode adicionar
        $canAdd = self::canAddMember($igrejaId);

        if (!$canAdd['allowed']) {
            // Registrar tentativa bloqueada
            SubscriptionHelper::logVerification(
                $igrejaId,
                'membros',
                'adicionar',
                'bloqueado_limite_excedido',
                array_merge($detalhes, [
                    'member_id' => $memberId,
                    'limite_atual' => $canAdd['limit'],
                    'consumo_atual' => $canAdd['current']
                ])
            );

            // Criar alerta se necessário
            if ($canAdd['current'] >= ($canAdd['limit'] * 0.9)) {
                SubscriptionHelper::createAlert(
                    $igrejaId,
                    'limite_proximo',
                    'Limite de Membros Próximo',
                    "Você tem {$canAdd['current']} de {$canAdd['limit']} membros. Considere fazer upgrade.",
                    ['recurso' => 'membros', 'consumo' => $canAdd['current'], 'limite' => $canAdd['limit']]
                );
            }

            return false;
        }

        // Registrar consumo
        $consumido = SubscriptionHelper::consumeResource($igrejaId, 'membros', 1);

        if ($consumido) {
            // Registrar verificação bem-sucedida
            SubscriptionHelper::logVerification(
                $igrejaId,
                'membros',
                'adicionar',
                'permitido',
                array_merge($detalhes, [
                    'member_id' => $memberId,
                    'consumo_apos' => $canAdd['current'] + 1
                ])
            );

            // Verificar se deve alertar sobre proximidade do limite
            $novoConsumo = $canAdd['current'] + 1;
            if ($canAdd['limit'] && $novoConsumo >= ($canAdd['limit'] * 0.8)) {
                SubscriptionHelper::createAlert(
                    $igrejaId,
                    'limite_proximo',
                    'Membros - Limite Próximo',
                    "Você tem {$novoConsumo} de {$canAdd['limit']} membros. Restam " . ($canAdd['limit'] - $novoConsumo) . " vagas.",
                    ['recurso' => 'membros', 'consumo' => $novoConsumo, 'limite' => $canAdd['limit']]
                );
            }
        }

        return $consumido;
    }

    /**
     * Verificar se pode remover membro (sempre permitido, apenas log)
     */
    public static function registerMemberRemoved(int $igrejaId, int $memberId, array $detalhes = []): void
    {
        // Remoção sempre é permitida, mas registramos para auditoria
        SubscriptionHelper::logVerification(
            $igrejaId,
            'membros',
            'remover',
            'permitido',
            array_merge($detalhes, ['member_id' => $memberId])
        );

        // Nota: Não decrementamos consumo pois membros removidos podem voltar
        // O consumo é mensal e reflete o máximo atingido no período
    }

    /**
     * Obter estatísticas de membros da igreja
     */
    public static function getMemberStats(int $igrejaId): array
    {
        $stats = SubscriptionHelper::getUsageStats($igrejaId);

        // Buscar contagem real de membros
        $igreja = Igreja::with('membros')->find($igrejaId);
        $totalMembros = $igreja ? $igreja->membros()->count() : 0;

        return [
            'assinatura_ativa' => $stats['assinatura']['ativo'] ?? false,
            'limite_membros' => $stats['recursos']['membros']['limite'] ?? 0,
            'consumo_atual' => $stats['recursos']['membros']['consumo'] ?? 0,
            'total_membros' => $totalMembros,
            'disponivel' => $stats['recursos']['membros']['disponivel'] ?? 0,
            'percentual_uso' => $stats['recursos']['membros']['percentual'] ?? 0,
            'bloqueado' => SubscriptionHelper::isResourceBlocked($igrejaId, 'membros'),
            'alertas' => array_filter($stats['alertas'] ?? [], function($alerta) {
                return isset($alerta['dados']['recurso']) && $alerta['dados']['recurso'] === 'membros';
            })
        ];
    }

    /**
     * Verificar se membros estão bloqueados
     */
    public static function isMembersBlocked(int $igrejaId): bool
    {
        return SubscriptionHelper::isResourceBlocked($igrejaId, 'membros');
    }

    /**
     * Bloquear adição de membros
     */
    public static function blockMembers(int $igrejaId, string $motivo, $user): void
    {
        SubscriptionHelper::blockResource($igrejaId, 'membros', $motivo, $user);

        // Criar alerta de bloqueio
        SubscriptionHelper::createAlert(
            $igrejaId,
            'limite_proximo',
            'Membros Bloqueados',
            "A adição de membros foi bloqueada: {$motivo}",
            ['motivo' => $motivo, 'recurso' => 'membros']
        );
    }

    /**
     * Desbloquear adição de membros
     */
    public static function unblockMembers(int $igrejaId, $user): void
    {
        SubscriptionHelper::unblockResource($igrejaId, 'membros', $user);

        // Criar alerta de desbloqueio
        SubscriptionHelper::createAlert(
            $igrejaId,
            'limite_proximo',
            'Membros Desbloqueados',
            "A adição de membros foi desbloqueada.",
            ['recurso' => 'membros', 'acao' => 'desbloqueado']
        );
    }

    /**
     * Verificar se membro pode ser adicionado (validações adicionais)
     */
    public static function validateMemberAddition(int $igrejaId, array $memberData): array
    {
        $erros = [];

        // Verificar assinatura
        if (!SubscriptionHelper::hasActiveSubscription($igrejaId)) {
            $erros[] = 'Igreja não possui assinatura ativa';
        }

        // Verificar limite de membros
        $canAdd = self::canAddMember($igrejaId);
        if (!$canAdd['allowed']) {
            $erros[] = "Limite de membros excedido ({$canAdd['current']}/{$canAdd['limit']})";
        }

        // Verificar se membros estão bloqueados
        if (self::isMembersBlocked($igrejaId)) {
            $erros[] = 'Adição de membros está bloqueada para esta igreja';
        }

        // Verificar dados obrigatórios
        $camposObrigatorios = ['user_id', 'cargo'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($memberData[$campo])) {
                $erros[] = "Campo obrigatório faltando: {$campo}";
            }
        }

        // Verificar se usuário já é membro desta igreja
        if (!empty($memberData['user_id'])) {
            $jaMembro = IgrejaMembro::where('igreja_id', $igrejaId)
                ->where('user_id', $memberData['user_id'])
                ->where('status', 'ativo')
                ->exists();

            if ($jaMembro) {
                $erros[] = 'Usuário já é membro ativo desta igreja';
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros,
            'limite_info' => $canAdd
        ];
    }

    /**
     * Obter histórico de adições de membros
     */
    public static function getMemberAdditionHistory(int $igrejaId, int $dias = 30): array
    {
        $igreja = Igreja::find($igrejaId);
        if (!$igreja) {
            return ['error' => 'Igreja não encontrada'];
        }

        $historico = $igreja->assinaturaVerificacoes()
            ->where('recurso_solicitado', 'membros')
            ->where('acao_solicitada', 'adicionar')
            ->where('verificado_em', '>=', now()->subDays($dias))
            ->orderBy('verificado_em', 'desc')
            ->get();

        $resumo = [
            'total_adicionados' => 0,
            'total_bloqueados' => 0,
            'dias_analisados' => $dias,
            'historico' => []
        ];

        foreach ($historico as $registro) {
            if ($registro->isPermitida()) {
                $resumo['total_adicionados']++;
            } else {
                $resumo['total_bloqueados']++;
            }

            $resumo['historico'][] = [
                'data' => $registro->verificado_em->format('d/m/Y H:i'),
                'acao' => $registro->acao_solicitada,
                'status' => $registro->status_verificacao,
                'member_id' => $registro->getValorDetalhes('member_id'),
                'detalhes' => $registro->detalhes
            ];
        }

        return $resumo;
    }

    /**
     * Calcular métricas de crescimento de membros
     */
    public static function calculateGrowthMetrics(int $igrejaId): array
    {
        $igreja = Igreja::find($igrejaId);
        if (!$igreja) {
            return ['error' => 'Igreja não encontrada'];
        }

        $hoje = now();
        $mesAtual = $hoje->format('Y-m');
        $mesPassado = $hoje->copy()->subMonth()->format('Y-m');

        // Contagem de membros atuais
        $totalAtual = $igreja->membros()->count();

        // Histórico de adições nos últimos 30 dias
        $adicoes30Dias = $igreja->assinaturaVerificacoes()
            ->where('recurso_solicitado', 'membros')
            ->where('acao_solicitada', 'adicionar')
            ->where('status_verificacao', 'permitido')
            ->where('verificado_em', '>=', $hoje->copy()->subDays(30))
            ->count();

        // Taxa de crescimento mensal (estimada)
        $taxaCrescimento = $totalAtual > 0 ? round(($adicoes30Dias / $totalAtual) * 100, 2) : 0;

        return [
            'total_membros' => $totalAtual,
            'adicoes_30_dias' => $adicoes30Dias,
            'taxa_crescimento_mensal' => $taxaCrescimento,
            'limite_atual' => self::getMemberStats($igrejaId)['limite_membros'] ?? 0,
            'percentual_ocupacao' => self::getMemberStats($igrejaId)['percentual_uso'] ?? 0,
            'data_calculo' => $hoje->format('d/m/Y')
        ];
    }
}
