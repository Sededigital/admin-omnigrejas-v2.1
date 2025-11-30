<?php

namespace App\Helpers\Billings;

use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Log;

class SmsSubscriptionHelper
{
    /**
     * Verificar se igreja pode enviar SMS
     */
    public static function canSendSms(int $igrejaId, int $quantidade = 1): array
    {
        return SubscriptionHelper::canConsumeResource($igrejaId, 'sms', $quantidade);
    }

    /**
     * Registrar envio de SMS
     */
    public static function registerSmsSent(int $igrejaId, int $quantidade = 1, array $detalhes = []): bool
    {
        // Verificar se pode enviar
        $canSend = self::canSendSms($igrejaId, $quantidade);

        if (!$canSend['allowed']) {
            // Registrar tentativa bloqueada
            SubscriptionHelper::logVerification(
                $igrejaId,
                'sms',
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
                    'Limite de SMS Próximo',
                    "Você utilizou {$canSend['current']} de {$canSend['limit']} SMS disponíveis. Considere fazer upgrade.",
                    ['recurso' => 'sms', 'consumo' => $canSend['current'], 'limite' => $canSend['limit']]
                );
            }

            return false;
        }

        // Registrar consumo
        $consumido = SubscriptionHelper::consumeResource($igrejaId, 'sms', $quantidade);

        if ($consumido) {
            // Registrar verificação bem-sucedida
            SubscriptionHelper::logVerification(
                $igrejaId,
                'sms',
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
                    'SMS - Limite Próximo',
                    "Você utilizou {$novoConsumo} de {$canSend['limit']} SMS. Restam " . ($canSend['limit'] - $novoConsumo) . " SMS.",
                    ['recurso' => 'sms', 'consumo' => $novoConsumo, 'limite' => $canSend['limit']]
                );
            }
        }

        return $consumido;
    }

    /**
     * Obter estatísticas de SMS da igreja
     */
    public static function getSmsStats(int $igrejaId): array
    {
        $stats = SubscriptionHelper::getUsageStats($igrejaId);

        return [
            'assinatura_ativa' => $stats['assinatura']['ativo'] ?? false,
            'limite_sms' => $stats['recursos']['sms']['limite'] ?? 0,
            'consumo_atual' => $stats['recursos']['sms']['consumo'] ?? 0,
            'disponivel' => $stats['recursos']['sms']['disponivel'] ?? 0,
            'percentual_uso' => $stats['recursos']['sms']['percentual'] ?? 0,
            'bloqueado' => SubscriptionHelper::isResourceBlocked($igrejaId, 'sms'),
            'alertas' => array_filter($stats['alertas'] ?? [], function($alerta) {
                return isset($alerta['dados']['recurso']) && $alerta['dados']['recurso'] === 'sms';
            })
        ];
    }

    /**
     * Verificar se SMS está bloqueado
     */
    public static function isSmsBlocked(int $igrejaId): bool
    {
        return SubscriptionHelper::isResourceBlocked($igrejaId, 'sms');
    }

    /**
     * Bloquear envio de SMS
     */
    public static function blockSms(int $igrejaId, string $motivo, $user): void
    {
        SubscriptionHelper::blockResource($igrejaId, 'sms', $motivo, $user);

        // Criar alerta de bloqueio
        SubscriptionHelper::createAlert(
            $igrejaId,
            'limite_proximo',
            'SMS Bloqueado',
            "O envio de SMS foi bloqueado: {$motivo}",
            ['motivo' => $motivo, 'recurso' => 'sms']
        );
    }

    /**
     * Desbloquear envio de SMS
     */
    public static function unblockSms(int $igrejaId, $user): void
    {
        SubscriptionHelper::unblockResource($igrejaId, 'sms', $user);

        // Criar alerta de desbloqueio
        SubscriptionHelper::createAlert(
            $igrejaId,
            'limite_proximo',
            'SMS Desbloqueado',
            "O envio de SMS foi desbloqueado.",
            ['recurso' => 'sms', 'acao' => 'desbloqueado']
        );
    }

    /**
     * Calcular custo estimado de SMS
     */
    public static function calculateSmsCost(int $quantidade, string $tipo = 'padrao'): array
    {
        // Custos por tipo de SMS (exemplo)
        $custos = [
            'padrao' => 50,    // 50 kwanzas por SMS
            'marketing' => 50, // 50 kwanzas para marketing
            'transacional' => 50, // 50 kwanzas para transacional
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
     * Validar conteúdo do SMS
     */
    public static function validateSmsContent(string $conteudo): array
    {
        $erros = [];

        // Verificar tamanho (limite típico de 160 caracteres)
        if (strlen($conteudo) > 200) {
            $erros[] = 'SMS não pode ter mais de 160 caracteres';
        }

        // Verificar se não está vazio
        if (empty(trim($conteudo))) {
            $erros[] = 'Conteúdo do SMS não pode estar vazio';
        }

        // Verificar caracteres especiais
        if (preg_match('/[^\x20-\x7E\xA0-\xFF]/u', $conteudo)) {
            $erros[] = 'Conteúdo contém caracteres não suportados';
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros,
            'tamanho' => strlen($conteudo),
            'limite' => 200
        ];
    }

    /**
     * Obter histórico de uso de SMS
     */
    public static function getSmsUsageHistory(int $igrejaId, int $dias = 30): array
    {
        $igreja = Igreja::find($igrejaId);
        if (!$igreja) {
            return ['error' => 'Igreja não encontrada'];
        }

        $historico = $igreja->assinaturaVerificacoes()
            ->where('recurso_solicitado', 'sms')
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
}
