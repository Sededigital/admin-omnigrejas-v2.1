<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Outros\EngajamentoBadge;
use App\Models\Outros\EngajamentoPonto;

class EngajamentoService
{
    // Constantes de pontos por ação
    public const PONTOS = [
        'login_diario' => 5,
        'comentario_post' => 10,
        'reacao_post' => 2,
        'post_criado' => 15,
        'evento_participado' => 20,
        'mensagem_chat' => 3,
        'pedido_oracao' => 8,
        'doacao_online' => 25,
        'voluntario_escala' => 30,
        'curso_concluido' => 50,
        'badge_conquistado' => 100,
    ];

    // Sistema de badges baseado em pontos acumulados
    public const BADGES = [
        ['nome' => 'Iniciante', 'pontos_requeridos' => 50, 'icone' => '🌱', 'cor' => '#10B981'],
        ['nome' => 'Ativo', 'pontos_requeridos' => 200, 'icone' => '⚡', 'cor' => '#3B82F6'],
        ['nome' => 'Engajado', 'pontos_requeridos' => 500, 'icone' => '🔥', 'cor' => '#F59E0B'],
        ['nome' => 'Líder', 'pontos_requeridos' => 1000, 'icone' => '👑', 'cor' => '#8B5CF6'],
        ['nome' => 'Mestre', 'pontos_requeridos' => 2000, 'icone' => '🏆', 'cor' => '#EF4444'],
        ['nome' => 'Lenda', 'pontos_requeridos' => 5000, 'icone' => '💎', 'cor' => '#EC4899'],
    ];

    /**
     * Registra pontos para uma ação específica
     */
    public function registrarPontos(User $user, string $acao, ?int $pontosCustomizados = null, ?string $descricao = null): bool
    {
        try {
            // Verificar se usuário tem igreja vinculada
            $igrejaMembro = IgrejaMembro::where('user_id', $user->id)
                                      ->where('status', 'ativo')
                                      ->first();

            if (!$igrejaMembro) {
              //  Log::info("Usuário {$user->id} não tem igreja ativa - pontos não registrados");
                return false;
            }

            $pontos = $pontosCustomizados ?? (self::PONTOS[$acao] ?? 0);

            if ($pontos <= 0) {
             //   Log::warning("Ação '{$acao}' não tem pontos definidos ou pontos inválidos");
                return false;
            }

            // Registrar pontos
            EngajamentoPonto::create([
                'user_id' => $user->id,
                'igreja_id' => $igrejaMembro->igreja_id,
                'pontos' => $pontos,
                'motivo' => $acao,
                'data' => now(),
            ]);

            // Verificar se conquistou novo badge
            $this->verificarBadges($user, $igrejaMembro->igreja_id);

            Log::info("Pontos registrados: {$pontos} para {$user->name} - ação: {$acao}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao registrar pontos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica se usuário conquistou novos badges
     */
    public function verificarBadges(User $user, int $igrejaId): void
    {
        try {
            $pontosTotais = $this->getPontosTotais($user->id, $igrejaId);

            foreach (self::BADGES as $badgeConfig) {
                if ($pontosTotais >= $badgeConfig['pontos_requeridos']) {
                    $this->concederBadge($user, $igrejaId, $badgeConfig);
                }
            }
        } catch (\Exception $e) {
            Log::error("Erro ao verificar badges: " . $e->getMessage());
        }
    }

    /**
     * Concede um badge ao usuário (se não tiver já)
     */
    private function concederBadge(User $user, int $igrejaId, array $badgeConfig): void
    {
        $badgeExistente = EngajamentoBadge::where('user_id', $user->id)
                                         ->where('igreja_id', $igrejaId)
                                         ->where('badge', $badgeConfig['nome'])
                                         ->exists();

        if (!$badgeExistente) {
            EngajamentoBadge::create([
                'user_id' => $user->id,
                'igreja_id' => $igrejaId,
                'badge' => $badgeConfig['nome'],
                'descricao' => "Badge conquistado por acumular {$badgeConfig['pontos_requeridos']} pontos",
                'data' => now(),
            ]);

            Log::info("Badge '{$badgeConfig['nome']}' concedido para {$user->name}");

            // Registrar pontos extras por conquistar badge
            $this->registrarPontos($user, 'badge_conquistado', null, "Conquistou badge: {$badgeConfig['nome']}");
        }
    }

    /**
     * Obtém pontos totais do usuário
     */
    public function getPontosTotais(string $userId, int $igrejaId): int
    {
        return EngajamentoPonto::where('user_id', $userId)
                              ->where('igreja_id', $igrejaId)
                              ->sum('pontos');
    }

    /**
     * Obtém ranking de usuários por pontos
     */
    public function getRanking(int $igrejaId, int $limite = 50): array
    {
        return DB::select("
            SELECT
                u.id,
                u.name,
                u.photo_url,
                SUM(ep.pontos) as pontos_totais,
                COUNT(ep.id) as total_acoes,
                MAX(ep.created_at) as ultima_atividade
            FROM users u
            JOIN engajamento_pontos ep ON ep.user_id = u.id
            WHERE ep.igreja_id = ?
            GROUP BY u.id, u.name, u.photo_url
            ORDER BY pontos_totais DESC, ultima_atividade DESC
            LIMIT ?
        ", [$igrejaId, $limite]);
    }

    /**
     * Verifica se usuário já fez login hoje (para pontos de login diário)
     */
    public function fezLoginHoje(User $user): bool
    {
        return EngajamentoPonto::where('user_id', $user->id)
                              ->where('motivo', 'login_diario')
                              ->whereDate('created_at', today())
                              ->exists();
    }

    /**
     * Registra login diário (se ainda não fez hoje)
     */
    public function registrarLoginDiario(User $user): bool
    {
        try {
            // Verificar se já fez login hoje
            if ($this->fezLoginHoje($user)) {
               // Log::info("Usuário {$user->id} já fez login hoje - pontos não registrados");
                return false;
            }

            // Registrar pontos
            $resultado = $this->registrarPontos($user, 'login_diario', null, 'Login diário no sistema');

            if ($resultado) {
               // Log::info("Pontos de login diário registrados para usuário {$user->id}");
            } else {
                Log::warning("Falha ao registrar pontos de login diário para usuário {$user->id}");
            }

            return $resultado;

        } catch (\Exception $e) {
            // Log::error("Erro ao registrar login diário para usuário {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtém estatísticas de engajamento do usuário
     */
    public function getEstatisticasUsuario(string $userId, int $igrejaId): array
    {
        $pontosTotais = $this->getPontosTotais($userId, $igrejaId);
        $totalAcoes = EngajamentoPonto::where('user_id', $userId)->where('igreja_id', $igrejaId)->count();
        $badges = EngajamentoBadge::where('user_id', $userId)->where('igreja_id', $igrejaId)->count();

        // Próximo badge
        $proximoBadge = null;
        foreach (self::BADGES as $badge) {
            if ($pontosTotais < $badge['pontos_requeridos']) {
                $proximoBadge = [
                    'nome' => $badge['nome'],
                    'pontos_requeridos' => $badge['pontos_requeridos'],
                    'pontos_faltando' => $badge['pontos_requeridos'] - $pontosTotais,
                    'icone' => $badge['icone'],
                    'cor' => $badge['cor'],
                ];
                break;
            }
        }

        return [
            'pontos_totais' => $pontosTotais,
            'total_acoes' => $totalAcoes,
            'badges_conquistados' => $badges,
            'proximo_badge' => $proximoBadge,
            'nivel_atual' => $this->getNivelAtual($pontosTotais),
        ];
    }

    /**
     * Determina o nível atual baseado nos pontos
     */
    private function getNivelAtual(int $pontos): string
    {
        foreach (array_reverse(self::BADGES) as $badge) {
            if ($pontos >= $badge['pontos_requeridos']) {
                return $badge['nome'];
            }
        }
        return 'Iniciante';
    }
}
