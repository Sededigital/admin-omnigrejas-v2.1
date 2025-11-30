<?php

namespace App\Helpers\Billings;

use App\Models\Billings\AssinaturaAtual;
use App\Models\IgrejaConsumo;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SubscriptionHelper
{
    /**
     * Verificar se igreja tem assinatura ativa
     */
    public static function hasActiveSubscription(int $igrejaId): bool
    {
        return Cache::remember(
            "subscription_active_{$igrejaId}",
            300, // 5 minutos
            function () use ($igrejaId) {
                return AssinaturaAtual::where('igreja_id', $igrejaId)
                    ->where('status', 'Ativo')
                    ->where(function ($query) {
                        $query->where('vitalicio', true)
                              ->orWhere('data_fim', '>=', now());
                    })
                    ->exists();
            }
        );
    }

    /**
     * Verificar se tem nível mínimo
     */
    public static function hasMinimumLevel(int $igrejaId, string $requiredLevel): bool
    {
        $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)
            ->where('status', 'Ativo')
            ->with('pacote.niveis')
            ->first();

        if (!$assinatura) {
            return false;
        }

        return $assinatura->pacote->niveis()
            ->where('nivel', $requiredLevel)
            ->exists();
    }

    /**
     * Verificar se pode consumir recurso
     */
    public static function canConsumeResource(int $igrejaId, string $resourceType, int $cost = 1): array
    {
        // Buscar limite atual da assinatura
        $limite = self::getResourceLimit($igrejaId, $resourceType);

        if ($limite === null) {
            // Ilimitado
            return ['allowed' => true, 'current' => 0, 'limit' => null];
        }

        // Buscar consumo atual
        $consumoAtual = self::getCurrentConsumption($igrejaId, $resourceType);

        $novoConsumo = $consumoAtual + $cost;
        $allowed = $novoConsumo <= $limite;

        return [
            'allowed' => $allowed,
            'current' => $consumoAtual,
            'limit' => $limite,
            'would_be' => $novoConsumo
        ];
    }

    /**
     * Registrar consumo de recurso
     */
    public static function consumeResource(int $igrejaId, string $resourceType, int $cost = 1): bool
    {
        $igreja = Igreja::find($igrejaId);
        if (!$igreja) return false;

        $result = $igreja->consumirRecurso($resourceType, $cost);

        // Limpar cache
        Cache::forget("consumption_{$igrejaId}_{$resourceType}");

        return $result;
    }

    /**
     * Obter limite de recurso
     */
    private static function getResourceLimit(int $igrejaId, string $resourceType): ?int
    {
        return Cache::remember(
            "resource_limit_{$igrejaId}_{$resourceType}",
            3600, // 1 hora
            function () use ($igrejaId, $resourceType) {
                $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)
                    ->where('status', 'Ativo')
                    ->with('pacote.recursos')
                    ->first();

                if (!$assinatura) {
                    return 0; // Sem assinatura = sem limite
                }

                $recurso = $assinatura->pacote->recursos()
                    ->where('recurso_tipo', $resourceType)
                    ->where('ativo', true)
                    ->first();

                return $recurso ? $recurso->limite_valor : 0;
            }
        );
    }

    /**
     * Obter consumo atual
     */
    private static function getCurrentConsumption(int $igrejaId, string $resourceType): int
    {
        return Cache::remember(
            "consumption_{$igrejaId}_{$resourceType}",
            300, // 5 minutos
            function () use ($igrejaId, $resourceType) {
                return IgrejaConsumo::where('igreja_id', $igrejaId)
                    ->where('recurso_tipo', $resourceType)
                    ->where('periodo_referencia', now()->format('Y-m-01'))
                    ->value('consumo_atual') ?? 0;
            }
        );
    }

    /**
     * Registrar verificação de assinatura
     */
    public static function logVerification(int $igrejaId, string $recurso, string $acao, string $status, array $detalhes = [], $user = null): void
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            $igreja->registrarVerificacao($recurso, $acao, $status, $detalhes, $user);
        }
    }

    /**
     * Criar alerta para igreja
     */
    public static function createAlert(int $igrejaId, string $tipo, string $titulo, string $mensagem, array $dados = [], int $diasExpiracao = 7): void
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            $igreja->criarAlerta($tipo, $titulo, $mensagem, $dados, $diasExpiracao);
        }
    }

    /**
     * Criar alerta (alias para createAlert)
     */
    public static function criarAlerta(int $igrejaId, string $tipo, string $titulo, string $mensagem, $expires_at = null): void
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            $igreja->criarAlerta($tipo, $titulo, $mensagem, [], $expires_at ? now()->diffInDays($expires_at) : 7);
        }
    }

    /**
     * Enviar alerta imediatamente
     */
    public static function enviarAlertaImediatamente($alerta): void
    {
        // Implementar envio imediato do alerta
        // Por enquanto, apenas marcar como enviado
        if ($alerta && method_exists($alerta, 'update')) {
            $alerta->update(['enviado_em' => now()]);
        }
    }

    /**
     * Verificar status completo da assinatura
     */
    public static function checkSubscriptionStatus(int $igrejaId): array
    {
        $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)->first();

        if (!$assinatura) {
            return [
                'active' => false,
                'status' => 'no_subscription',
                'message' => 'Nenhuma assinatura encontrada',
                'details' => []
            ];
        }

        $statusDetalhado = $assinatura->getStatusDetalhado();

        return [
            'active' => $statusDetalhado['ativo'],
            'status' => $statusDetalhado['status'],
            'message' => self::getStatusMessage($statusDetalhado),
            'details' => $statusDetalhado
        ];
    }

    /**
     * Obter mensagem amigável do status
     */
    private static function getStatusMessage(array $status): string
    {
        if (!$status['ativo']) {
            if ($status['status'] === 'Cancelado') {
                return 'Assinatura cancelada';
            }
            if ($status['status'] === 'Expirado') {
                return 'Assinatura expirada';
            }
            return 'Assinatura inativa';
        }

        if ($status['expirando_em_breve']) {
            $dias = $status['dias_para_expirar'];
            return "Assinatura expira em {$dias} dia" . ($dias > 1 ? 's' : '');
        }

        if ($status['em_trial']) {
            $dias = $status['dias_trial_restantes'];
            return "Período de teste termina em {$dias} dia" . ($dias > 1 ? 's' : '');
        }

        return 'Assinatura ativa';
    }

    /**
     * Verificar se recurso está bloqueado
     */
    public static function isResourceBlocked(int $igrejaId, string $resourceType): bool
    {
        return Igreja::find($igrejaId)?->temRecursoBloqueado($resourceType) ?? false;
    }

    /**
     * Bloquear recurso
     */
    public static function blockResource(int $igrejaId, string $resourceType, string $motivo, $user): void
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            $igreja->bloquearRecurso($resourceType, $motivo, $user->id);
        }
    }

    /**
     * Bloquear recurso (alias em português)
     */
    public static function bloquearRecurso(int $igrejaId, string $resourceType, string $motivo, string $observacoes = null): void
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            $igreja->bloquearRecurso($resourceType, $motivo, Auth::id(), $observacoes);
        }
    }

    /**
     * Desbloquear recurso
     */
    public static function unblockResource(int $igrejaId, string $resourceType, $user): void
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            $igreja->desbloquearRecurso($resourceType, $user->id);
        }
    }

    /**
     * Desbloquear recurso (alias em português)
     */
    public static function desbloquearRecurso(int $igrejaId, string $resourceType): void
    {
        $igreja = Igreja::find($igrejaId);
        if ($igreja) {
            $igreja->desbloquearRecurso($resourceType, Auth::id());
        }
    }

    /**
     * Obter estatísticas de uso
     */
    public static function getUsageStats(int $igrejaId): array
    {
        $igreja = Igreja::find($igrejaId);
        if (!$igreja) {
            return ['error' => 'Igreja não encontrada'];
        }

        return [
            'assinatura' => $igreja->getStatusAssinatura(),
            'recursos' => $igreja->getUsoRecursos(),
            'alertas' => $igreja->getAlertasAtivos(),
            'bloqueios' => $igreja->getRecursosBloqueados(),
        ];
    }

    /**
     * Limpar cache de uma igreja
     */
    public static function clearCache(int $igrejaId): void
    {
        // Limpar cache de assinatura
        Cache::forget("subscription_active_{$igrejaId}");

        // Limpar cache de recursos (buscar dinamicamente todos os tipos)
        $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)->with('pacote.recursos')->first();
        if ($assinatura && $assinatura->pacote) {
            foreach ($assinatura->pacote->recursos as $recurso) {
                Cache::forget("resource_limit_{$igrejaId}_{$recurso->recurso_tipo}");
                Cache::forget("consumption_{$igrejaId}_{$recurso->recurso_tipo}");
            }
        }
    }

    /**
     * Verificar se deve alertar sobre limites
     */
    public static function shouldAlertAboutLimits(int $igrejaId): array
    {
        $stats = self::getUsageStats($igrejaId);
        $alertas = [];

        if (isset($stats['recursos'])) {
            foreach ($stats['recursos'] as $tipo => $dados) {
                if ($dados['percentual'] >= 90) {
                    $alertas[] = [
                        'tipo' => 'limite_proximo',
                        'titulo' => 'Limite Próximo',
                        'mensagem' => "O uso de {$tipo} está em {$dados['percentual']}%. Considere fazer upgrade.",
                        'dados' => $dados
                    ];
                }
            }
        }

        return $alertas;
    }

    /**
     * Obter contagem de alertas não lidos
     */
    public static function getUnreadAlertsCount(?int $igrejaId): int
    {
        if (!$igrejaId) {
            return 0;
        }

        return Cache::remember(
            "unread_alerts_count_{$igrejaId}",
            300, // 5 minutos
            function () use ($igrejaId) {
                return \App\Models\Billings\AssinaturaAlertas::where('igreja_id', $igrejaId)
                    ->where('lido', false)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    })
                    ->count();
            }
        );
    }

    /**
     * Obter alertas ativos
     */
    public static function getActiveAlerts(?int $igrejaId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        if (!$igrejaId) {
            return collect();
        }

        return \App\Models\Billings\AssinaturaAlertas::where('igreja_id', $igrejaId)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
