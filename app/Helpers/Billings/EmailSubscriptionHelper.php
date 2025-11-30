<?php

namespace App\Helpers\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Log;

class EmailSubscriptionHelper
{
    /**
     * Verificar se igreja pode enviar emails
     */
    public static function canSendEmail(int $igrejaId, int $quantidade = 1): array
    {
        return SubscriptionHelper::canConsumeResource($igrejaId, 'emails', $quantidade);
    }

    /**
     * Registrar envio de email
     */
    public static function registerEmailSent(int $igrejaId, int $quantidade = 1, array $detalhes = []): bool
    {
        // Verificar se pode enviar
        $canSend = self::canSendEmail($igrejaId, $quantidade);

        if (!$canSend['allowed']) {
            // Registrar tentativa bloqueada
            SubscriptionHelper::logVerification(
                $igrejaId,
                'emails',
                'enviar',
                'bloqueado_limite_excedido',
                array_merge($detalhes, [
                    'quantidade_solicitada' => $quantidade,
                    'limite_atual' => $canSend['limit'],
                    'consumo_atual' => $canSend['current']
                ])
            );

            // Criar alerta se necessário
            if ($canSend['current'] >= ($canSend['limit'] * 0.9)) {
                SubscriptionHelper::createAlert(
                    $igrejaId,
                    'limite_proximo',
                    'Limite de Emails Próximo',
                    "Você utilizou {$canSend['current']} de {$canSend['limit']} emails disponíveis. Considere fazer upgrade.",
                    ['recurso' => 'emails', 'consumo' => $canSend['current'], 'limite' => $canSend['limit']]
                );
            }

            return false;
        }

        // Registrar consumo
        $consumido = SubscriptionHelper::consumeResource($igrejaId, 'emails', $quantidade);

        if ($consumido) {
            // Registrar verificação bem-sucedida
            SubscriptionHelper::logVerification(
                $igrejaId,
                'emails',
                'enviar',
                'permitido',
                array_merge($detalhes, [
                    'quantidade_enviada' => $quantidade,
                    'consumo_apos' => $canSend['current'] + $quantidade
                ])
            );

            // Verificar se deve alertar sobre proximidade do limite
            $novoConsumo = $canSend['current'] + $quantidade;
            if ($canSend['limit'] && $novoConsumo >= ($canSend['limit'] * 0.8)) {
                SubscriptionHelper::createAlert(
                    $igrejaId,
                    'limite_proximo',
                    'Emails - Limite Próximo',
                    "Você utilizou {$novoConsumo} de {$canSend['limit']} emails. Restam " . ($canSend['limit'] - $novoConsumo) . " emails.",
                    ['recurso' => 'emails', 'consumo' => $novoConsumo, 'limite' => $canSend['limit']]
                );
            }
        }

        return $consumido;
    }

    /**
     * Obter estatísticas de emails da igreja
     */
    public static function getEmailStats(int $igrejaId): array
    {
        $stats = SubscriptionHelper::getUsageStats($igrejaId);

        return [
            'assinatura_ativa' => $stats['assinatura']['ativo'] ?? false,
            'limite_emails' => $stats['recursos']['emails']['limite'] ?? 0,
            'consumo_atual' => $stats['recursos']['emails']['consumo'] ?? 0,
            'disponivel' => $stats['recursos']['emails']['disponivel'] ?? 0,
            'percentual_uso' => $stats['recursos']['emails']['percentual'] ?? 0,
            'bloqueado' => SubscriptionHelper::isResourceBlocked($igrejaId, 'emails'),
            'alertas' => array_filter($stats['alertas'] ?? [], function($alerta) {
                return isset($alerta['dados']['recurso']) && $alerta['dados']['recurso'] === 'emails';
            })
        ];
    }

    /**
     * Verificar se emails estão bloqueados
     */
    public static function isEmailsBlocked(int $igrejaId): bool
    {
        return SubscriptionHelper::isResourceBlocked($igrejaId, 'emails');
    }

    /**
     * Bloquear envio de emails
     */
    public static function blockEmails(int $igrejaId, string $motivo, $user): void
    {
        SubscriptionHelper::blockResource($igrejaId, 'emails', $motivo, $user);

        // Criar alerta de bloqueio
        SubscriptionHelper::createAlert(
            $igrejaId,
            'limite_proximo',
            'Emails Bloqueados',
            "O envio de emails foi bloqueado: {$motivo}",
            ['motivo' => $motivo, 'recurso' => 'emails']
        );
    }

    /**
     * Desbloquear envio de emails
     */
    public static function unblockEmails(int $igrejaId, $user): void
    {
        SubscriptionHelper::unblockResource($igrejaId, 'emails', $user);

        // Criar alerta de desbloqueio
        SubscriptionHelper::createAlert(
            $igrejaId,
            'limite_proximo',
            'Emails Desbloqueados',
            "O envio de emails foi desbloqueado.",
            ['recurso' => 'emails', 'acao' => 'desbloqueado']
        );
    }

    /**
     * Calcular custo estimado de emails
     */
    public static function calculateEmailCost(int $quantidade, string $tipo = 'padrao'): array
    {
        // Custos por tipo de email (exemplo)
        $custos = [
            'padrao' => 0.001,     // 0.1 centavos por email
            'marketing' => 0.002,  // 0.2 centavos para marketing
            'transacional' => 0.0005, // 0.05 centavos para transacional
        ];

        $custoUnitario = $custos[$tipo] ?? $custos['padrao'];
        $custoTotal = $quantidade * $custoUnitario;

        return [
            'quantidade' => $quantidade,
            'custo_unitario' => $custoUnitario,
            'custo_total' => $custoTotal,
            'tipo' => $tipo,
            'moeda' => 'AKZ'
        ];
    }

    /**
     * Validar conteúdo do email
     */
    public static function validateEmailContent(array $emailData): array
    {
        $erros = [];

        // Verificar campos obrigatórios
        $camposObrigatorios = ['destinatario', 'assunto', 'conteudo'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($emailData[$campo])) {
                $erros[] = "Campo obrigatório faltando: {$campo}";
            }
        }

        // Verificar formato do email
        if (!empty($emailData['destinatario']) && !filter_var($emailData['destinatario'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Endereço de email do destinatário inválido';
        }

        // Verificar tamanho do assunto
        if (!empty($emailData['assunto']) && strlen($emailData['assunto']) > 255) {
            $erros[] = 'Assunto não pode ter mais de 255 caracteres';
        }

        // Verificar tamanho do conteúdo
        if (!empty($emailData['conteudo']) && strlen($emailData['conteudo']) > 10000) {
            $erros[] = 'Conteúdo não pode ter mais de 10.000 caracteres';
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros,
            'tamanho_assunto' => strlen($emailData['assunto'] ?? ''),
            'tamanho_conteudo' => strlen($emailData['conteudo'] ?? ''),
            'limite_assunto' => 255,
            'limite_conteudo' => 10000
        ];
    }

    /**
     * Obter histórico de uso de emails
     */
    public static function getEmailUsageHistory(int $igrejaId, int $dias = 30): array
    {
        $igreja = Igreja::find($igrejaId);
        if (!$igreja) {
            return ['error' => 'Igreja não encontrada'];
        }

        $historico = $igreja->assinaturaVerificacoes()
            ->where('recurso_solicitado', 'emails')
            ->where('verificado_em', '>=', now()->subDays($dias))
            ->orderBy('verificado_em', 'desc')
            ->get();

        $resumo = [
            'total_enviados' => 0,
            'total_bloqueados' => 0,
            'dias_analisados' => $dias,
            'historico' => []
        ];

        foreach ($historico as $registro) {
            if ($registro->isPermitida()) {
                $resumo['total_enviados'] += $registro->getValorDetalhes('quantidade_enviada', 0);
            } else {
                $resumo['total_bloqueados'] += $registro->getValorDetalhes('quantidade_solicitada', 0);
            }

            $resumo['historico'][] = [
                'data' => $registro->verificado_em->format('d/m/Y H:i'),
                'acao' => $registro->acao_solicitada,
                'status' => $registro->status_verificacao,
                'quantidade' => $registro->getValorDetalhes('quantidade_enviada') ??
                               $registro->getValorDetalhes('quantidade_solicitada'),
                'detalhes' => $registro->detalhes
            ];
        }

        return $resumo;
    }

    /**
     * Calcular métricas de engajamento de emails
     */
    public static function calculateEngagementMetrics(int $igrejaId, int $dias = 30): array
    {
        $historico = self::getEmailUsageHistory($igrejaId, $dias);

        $totalEnviados = $historico['total_enviados'];
        $periodoDias = $historico['dias_analisados'];

        // Métricas básicas (em um sistema real, teríamos dados de abertura, cliques, etc.)
        $taxaEnvioDiaria = $periodoDias > 0 ? round($totalEnviados / $periodoDias, 2) : 0;

        // Simulação de métricas de engajamento (em produção viriam de provedores de email)
        $taxaAberturaEstimada = 25.0; // 25%
        $taxaCliqueEstimada = 3.5;   // 3.5%

        return [
            'periodo_dias' => $periodoDias,
            'total_emails_enviados' => $totalEnviados,
            'taxa_envio_diaria' => $taxaEnvioDiaria,
            'taxa_abertura_estimada' => $taxaAberturaEstimada,
            'taxa_clique_estimada' => $taxaCliqueEstimada,
            'emails_abertos_estimados' => round($totalEnviados * ($taxaAberturaEstimada / 100)),
            'emails_clicados_estimados' => round($totalEnviados * ($taxaCliqueEstimada / 100)),
            'data_calculo' => now()->format('d/m/Y')
        ];
    }
}
