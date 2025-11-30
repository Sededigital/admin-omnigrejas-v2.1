<?php

namespace App\Helpers\Billings;

use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\Pacote;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ResourceHelper
{
    /**
     * Verificar assinatura da igreja (logada ou do usuário)
     */
    public static function getIgrejaAssinatura(?int $igrejaId = null): ?AssinaturaAtual
    {
        $igrejaId = $igrejaId ?? self::getIgrejaIdUsuarioLogado();

        if (!$igrejaId) {
            return null;
        }

        return Cache::remember(
            "assinatura_atual_{$igrejaId}",
            600, // 10 minutos
            function () use ($igrejaId) {
                return AssinaturaAtual::where('igreja_id', $igrejaId)
                    ->where('status', 'Ativo')
                    ->with(['pacote.recursos', 'pacote.niveis'])
                    ->first();
            }
        );
    }

    /**
     * Obter pacote atual da igreja
     */
    public static function getPacoteAtual(?int $igrejaId = null): ?Pacote
    {
        $assinatura = self::getIgrejaAssinatura($igrejaId);

        // Se há assinatura paga, retorna o pacote dela
        if ($assinatura && $assinatura->estaAtiva()) {
            return $assinatura->pacote;
        }

        // Se não há assinatura paga mas há trial ativo, retorna o pacote premium
        if (self::isAssinaturaTrial($igrejaId)) {
            return Pacote::orderBy('preco', 'desc')->first();
        }

        return $assinatura?->pacote;
    }

    /**
     * Verificar se recurso está disponível no pacote atual e existe no RBAC
     */
    public static function hasRecursoDisponivel(string $recursoTipo, ?int $igrejaId = null): bool
    {
        // Primeiro verificar se existe no RBAC
        $permissaoRBAC = \App\Models\RBAC\IgrejaPermissao::where('codigo', $recursoTipo)
            ->where('ativo', true)
            ->first();

        if (!$permissaoRBAC) {
            return false;
        }

        // Se for trial, todos os recursos estão disponíveis
        if (self::isAssinaturaTrial($igrejaId)) {
            return true;
        }

        // Depois verificar se está disponível no pacote
        $pacote = self::getPacoteAtual($igrejaId);

        if (!$pacote) {
            return false;
        }

        return $pacote->recursos()
            ->where('recurso_tipo', $recursoTipo)
            ->where('ativo', true)
            ->exists();
    }

    /**
     * Obter limite de um recurso específico
     */
    public static function getLimiteRecurso(string $recursoTipo, ?int $igrejaId = null): ?int
    {
        $pacote = self::getPacoteAtual($igrejaId);

        if (!$pacote) {
            return 0;
        }

        $recurso = $pacote->recursos()
            ->where('recurso_tipo', $recursoTipo)
            ->where('ativo', true)
            ->first();

        return $recurso ? $recurso->limite_valor : 0;
    }

    /**
     * Verificar se recurso é ilimitado
     */
    public static function isRecursoIlimitado(string $recursoTipo, ?int $igrejaId = null): bool
    {
        $limite = self::getLimiteRecurso($recursoTipo, $igrejaId);
        return $limite === null;
    }

    /**
     * Obter todos os recursos disponíveis no pacote atual (validando RBAC)
     */
    public static function getRecursosDisponiveis(?int $igrejaId = null): array
    {
        // Se for trial, retorna recursos do pacote premium
        if (self::isAssinaturaTrial($igrejaId)) {
            return self::getRecursosTrialDisponiveis();
        }

        $pacote = self::getPacoteAtual($igrejaId);

        if (!$pacote) {
            return [];
        }

        return $pacote->recursos()
            ->where('ativo', true)
            ->whereHas('pacotePermissao', function($query) {
                $query->where('ativo', true);
            })
            ->get()
            ->map(function ($recurso) {
                return [
                    'tipo' => $recurso->recurso_tipo,
                    'nome' => $recurso->getTipoFormatado(),
                    'limite' => $recurso->limite_valor,
                    'unidade' => $recurso->unidade,
                    'ilimitado' => $recurso->isIlimitado(),
                    'icone' => $recurso->getIcone(),
                    'descricao' => $recurso->getDescricao(),
                ];
            })
            ->toArray();
    }

    /**
     * Verificar se pacote tem nível específico
     */
    public static function hasNivel(string $nivel, ?int $igrejaId = null): bool
    {
        // Se for trial, tem acesso a todos os níveis
        if (self::isAssinaturaTrial($igrejaId)) {
            return true;
        }

        $pacote = self::getPacoteAtual($igrejaId);

        if (!$pacote) {
            return false;
        }

        return $pacote->niveis()
            ->where('nivel', $nivel)
            ->exists();
    }

    /**
     * Obter nível máximo do pacote atual
     */
    public static function getNivelMaximo(?int $igrejaId = null): ?object
    {
        // Se for trial, retorna o nível máximo disponível
        if (self::isAssinaturaTrial($igrejaId)) {
            $pacotePremium = Pacote::orderBy('preco', 'desc')->first();
            if ($pacotePremium) {
                return $pacotePremium->niveis()
                    ->orderBy('prioridade', 'desc')
                    ->first();
            }
        }

        $pacote = self::getPacoteAtual($igrejaId);

        if (!$pacote) {
            return null;
        }

        return $pacote->niveis()
            ->orderBy('prioridade', 'desc')
            ->first();
    }

    /**
     * Verificar se assinatura está ativa (considerando trial)
     */
    public static function isAssinaturaAtiva(?int $igrejaId = null): bool
    {
        $assinatura = self::getIgrejaAssinatura($igrejaId);

        // Se há assinatura paga ativa, retorna true
        if ($assinatura && $assinatura->estaAtiva()) {
            return true;
        }

        // Se não há assinatura paga, verificar se há trial ativo
        if (!$assinatura) {
            $user = Auth::user();
            if ($user && $user->trial && $user->trial->isAtivo()) {
                return true;
            }
        }

        // Verificar trial na assinatura (se existir)
        return $assinatura && $assinatura->isTrialAtivo();
    }

    /**
     * Verificar se assinatura é trial
     */
    public static function isAssinaturaTrial(?int $igrejaId = null): bool
    {
        $assinatura = self::getIgrejaAssinatura($igrejaId);

        // Se há assinatura paga, verificar se é trial
        if ($assinatura) {
            return $assinatura->isTrialAtivo();
        }

        // Se não há assinatura paga, verificar diretamente no trial do usuário
        $user = Auth::user();
        return $user && $user->trial && $user->trial->isAtivo();
    }

    /**
     * Verificar se assinatura trial está expirando (dias restantes)
     */
    public static function getDiasRestantesTrial(?int $igrejaId = null): ?int
    {
        $assinatura = self::getIgrejaAssinatura($igrejaId);
        return $assinatura && $assinatura->isTrialAtivo() ? $assinatura->diasTrialRestantes() : null;
    }

    /**
     * Verificar se assinatura está expirando em breve (15 dias)
     */
    public static function isAssinaturaExpirando(?int $igrejaId = null, int $dias = 15): bool
    {
        $assinatura = self::getIgrejaAssinatura($igrejaId);
        return $assinatura && $assinatura->isExpiringSoon($dias);
    }

    /**
     * Obter dias restantes da assinatura
     */
    public static function getDiasRestantes(?int $igrejaId = null): ?int
    {
        $assinatura = self::getIgrejaAssinatura($igrejaId);
        return $assinatura ? $assinatura->diasParaExpirar() : null;
    }

    /**
     * Obter informações completas da assinatura (incluindo trial)
     */
    public static function getInfoAssinatura(?int $igrejaId = null): array
    {
        $assinatura = self::getIgrejaAssinatura($igrejaId);

        if (!$assinatura) {
            return [
                'ativa' => false,
                'trial' => false,
                'pacote' => null,
                'expira_em' => null,
                'dias_restantes' => null,
                'expirando_em_breve' => false,
                'recursos' => [],
                'nivel_maximo' => null,
                'tipo_assinatura' => 'nenhuma',
            ];
        }

        $isTrial = $assinatura->isTrialAtivo();
        $isAtiva = $assinatura->isAtiva() || $isTrial;

        return [
            'ativa' => $isAtiva,
            'trial' => $isTrial,
            'pacote' => [
                'id' => $assinatura->pacote->id,
                'nome' => $assinatura->pacote->nome,
                'preco' => $assinatura->pacote->preco,
                'preco_formatado' => $assinatura->pacote->getPrecoFormatado(),
            ],
            'expira_em' => $assinatura->data_fim?->format('d/m/Y'),
            'dias_restantes' => $isTrial ? $assinatura->diasTrialRestantes() : $assinatura->diasParaExpirar(),
            'expirando_em_breve' => $assinatura->isExpiringSoon(15),
            'recursos' => $isTrial ? self::getRecursosTrialDisponiveis() : self::getRecursosDisponiveis($igrejaId),
            'nivel_maximo' => self::getNivelMaximo($igrejaId),
            'tipo_assinatura' => $isTrial ? 'trial' : 'paga',
        ];
    }

    /**
     * Obter recursos disponíveis durante trial (todos os recursos do pacote premium)
     */
    private static function getRecursosTrialDisponiveis(): array
    {
        // Obter o pacote com mais recursos (mais caro)
        $pacotePremium = Pacote::with(['recursos' => function($query) {
            $query->where('ativo', true);
        }])
        ->orderBy('preco', 'desc')
        ->first();

        if (!$pacotePremium) {
            // Fallback: todos os recursos do RBAC se não houver pacotes
            return \App\Models\RBAC\IgrejaPermissao::where('ativo', true)
                ->get()
                ->map(function ($permissao) {
                    return [
                        'tipo' => $permissao->codigo,
                        'nome' => $permissao->nome,
                        'limite' => null, // Trial = ilimitado
                        'unidade' => 'ilimitado',
                        'ilimitado' => true,
                        'icone' => 'fas fa-crown', // Ícone premium
                        'descricao' => $permissao->descricao ?? 'Recurso premium disponível no trial',
                    ];
                })
                ->toArray();
        }

        // Retornar recursos do pacote premium, mas como ilimitados para trial
        return $pacotePremium->recursos()
            ->where('ativo', true)
            ->whereHas('pacotePermissao', function($query) {
                $query->where('ativo', true);
            })
            ->get()
            ->map(function ($recurso) {
                return [
                    'tipo' => $recurso->recurso_tipo,
                    'nome' => $recurso->getTipoFormatado(),
                    'limite' => null, // Trial sempre ilimitado
                    'unidade' => 'ilimitado',
                    'ilimitado' => true,
                    'icone' => $recurso->getIcone(),
                    'descricao' => $recurso->getDescricao() . ' (Premium Trial)',
                ];
            })
            ->toArray();
    }

    /**
     * Verificar se pode fazer upgrade para um pacote
     */
    public static function canUpgradeTo(Pacote $pacoteAlvo, ?int $igrejaId = null): bool
    {
        $pacoteAtual = self::getPacoteAtual($igrejaId);

        if (!$pacoteAtual) {
            return true; // Sem pacote atual, pode assinar qualquer um
        }

        // Só pode fazer upgrade se o preço for maior
        return $pacoteAlvo->preco > $pacoteAtual->preco;
    }

    /**
     * Obter pacotes disponíveis para upgrade
     */
    public static function getPacotesUpgradeDisponiveis(?int $igrejaId = null): array
    {
        $pacoteAtual = self::getPacoteAtual($igrejaId);

        if (!$pacoteAtual) {
            // Sem pacote atual, todos são disponíveis
            return Pacote::orderBy('preco')->get()->toArray();
        }

        // Pacotes com preço maior (upgrade)
        return Pacote::where('preco', '>', $pacoteAtual->preco)
            ->orderBy('preco')
            ->get()
            ->toArray();
    }

    /**
     * Limpar cache de recursos da igreja
     */
    public static function clearCache(?int $igrejaId = null): void
    {
        $igrejaId = $igrejaId ?? self::getIgrejaIdUsuarioLogado();

        if ($igrejaId) {
            Cache::forget("assinatura_atual_{$igrejaId}");
            SubscriptionHelper::clearCache($igrejaId);
        }
    }

    /**
     * Obter ID da igreja do usuário logado
     */
    private static function getIgrejaIdUsuarioLogado(): ?int
    {
        $user = Auth::user();
        return $user ? $user->getIgrejaId() : null;
    }

    /**
     * Verificar se usuário tem permissão para um recurso específico (Pacote + RBAC + Trial)
     */
    public static function userCanAccessResource(string $recursoTipo, ?int $igrejaId = null): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Verificar se assinatura está ativa (incluindo trial)
        if (!self::isAssinaturaAtiva($igrejaId)) {
            return false;
        }

        // Se for trial, todos os recursos estão desbloqueados
        if (self::isAssinaturaTrial($igrejaId)) {
            // Verificar apenas se existe no RBAC (trial dá acesso total)
            $permissaoRBAC = \App\Models\RBAC\IgrejaPermissao::where('codigo', $recursoTipo)
                ->where('ativo', true)
                ->first();

            return $permissaoRBAC !== null;
        }

        // Para assinaturas pagas, verificar normalmente
        // Verificar se recurso está disponível no pacote
        if (!self::hasRecursoDisponivel($recursoTipo, $igrejaId)) {
            return false;
        }

        // Verificar se recurso não está bloqueado
        if (SubscriptionHelper::isResourceBlocked($igrejaId ?? self::getIgrejaIdUsuarioLogado(), $recursoTipo)) {
            return false;
        }

        // Verificar se usuário tem permissão RBAC para o recurso
        $permissionHelper = new \App\Helpers\RBAC\PermissionHelper($user);
        if (!$permissionHelper->hasPermission($recursoTipo)) {
            return false;
        }

        return true;
    }

    /**
     * Obter estatísticas de uso de recursos (considerando trial)
     */
    public static function getEstatisticasUso(?int $igrejaId = null): array
    {
        $igrejaId = $igrejaId ?? self::getIgrejaIdUsuarioLogado();

        if (!$igrejaId) {
            return ['error' => 'Igreja não encontrada'];
        }

        $isTrial = self::isAssinaturaTrial($igrejaId);

        if ($isTrial) {
            // Para trial, mostrar estatísticas especiais
            return self::getEstatisticasTrial($igrejaId);
        }

        $stats = SubscriptionHelper::getUsageStats($igrejaId);

        if (isset($stats['error'])) {
            return $stats;
        }

        $recursosFormatados = [];
        foreach ($stats['recursos'] ?? [] as $tipo => $dados) {
            $limitePacote = self::getLimiteRecurso($tipo, $igrejaId);
            $recursosFormatados[$tipo] = array_merge($dados, [
                'limite_pacote' => $limitePacote,
                'ilimitado' => $limitePacote === null,
                'status' => self::getStatusRecurso($tipo, $dados, $igrejaId),
            ]);
        }

        return [
            'assinatura' => $stats['assinatura'] ?? [],
            'recursos' => $recursosFormatados,
            'alertas' => $stats['alertas'] ?? [],
            'bloqueios' => $stats['bloqueios'] ?? [],
            'resumo' => [
                'total_recursos' => count($recursosFormatados),
                'recursos_bloqueados' => count(array_filter($recursosFormatados, fn($r) => $r['bloqueado'] ?? false)),
                'recursos_proximo_limite' => count(array_filter($recursosFormatados, fn($r) => ($r['percentual'] ?? 0) >= 80)),
                'tipo_assinatura' => 'paga',
            ]
        ];
    }

    /**
     * Obter estatísticas especiais para trial
     */
    private static function getEstatisticasTrial(?int $igrejaId): array
    {
        $diasRestantes = self::getDiasRestantesTrial($igrejaId);
        $recursosTrial = self::getRecursosTrialDisponiveis();

        return [
            'assinatura' => [
                'ativa' => true,
                'trial' => true,
                'dias_restantes_trial' => $diasRestantes,
                'tipo_assinatura' => 'trial',
            ],
            'recursos' => array_map(function($recurso) {
                return array_merge($recurso, [
                    'consumo' => 0, // Trial não conta consumo
                    'limite_pacote' => null,
                    'ilimitado' => true,
                    'percentual' => 0,
                    'disponivel' => null,
                    'bloqueado' => false,
                    'status' => 'trial_ilimitado',
                ]);
            }, $recursosTrial),
            'alertas' => [],
            'bloqueios' => [],
            'resumo' => [
                'total_recursos' => count($recursosTrial),
                'recursos_bloqueados' => 0, // Trial nunca bloqueia
                'recursos_proximo_limite' => 0, // Trial não tem limites
                'tipo_assinatura' => 'trial',
                'dias_restantes_trial' => $diasRestantes,
            ]
        ];
    }

    /**
     * Verificar se usuário pode acessar uma seção baseada nos recursos disponíveis
     */
    public static function userCanAccessSection(array $requiredResources, ?int $igrejaId = null): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Verificar se assinatura está ativa (incluindo trial)
        if (!self::isAssinaturaAtiva($igrejaId)) {
            return false;
        }

        // Se for trial, todas as seções estão disponíveis
        if (self::isAssinaturaTrial($igrejaId)) {
            return true;
        }

        // Para assinaturas pagas, verificar se pelo menos um recurso da seção está disponível
        foreach ($requiredResources as $resourceTipo) {
            if (self::hasRecursoDisponivel($resourceTipo, $igrejaId)) {
                // Verificar se não está bloqueado
                if (!SubscriptionHelper::isResourceBlocked($igrejaId ?? self::getIgrejaIdUsuarioLogado(), $resourceTipo)) {
                    // Verificar se usuário tem permissão RBAC
                    $permissionHelper = new \App\Helpers\RBAC\PermissionHelper($user);
                    if ($permissionHelper->hasPermission($resourceTipo)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Obter status de um recurso específico
     */
    private static function getStatusRecurso(string $tipo, array $dados, ?int $igrejaId): string
    {
        if ($dados['bloqueado'] ?? false) {
            return 'bloqueado';
        }

        if (($dados['percentual'] ?? 0) >= 90) {
            return 'critico';
        }

        if (($dados['percentual'] ?? 0) >= 80) {
            return 'alerta';
        }

        if (self::isRecursoIlimitado($tipo, $igrejaId)) {
            return 'ilimitado';
        }

        return 'normal';
    }
}