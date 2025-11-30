<?php

namespace App\Http\Middleware\RBAC;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\RBAC\IgrejaPermissaoLog;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role, string $requireAll = 'false'): Response
    {
        $user = Auth::user();

        // 1. Verificar se usuário está autenticado
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        // 2. Verificar se tem a função através do Gate
        if (Gate::allows('has-role', [$user, $role])) {
            // Registrar log de acesso autorizado
            $this->logAccessGranted($user, $role, $request);
            return $next($request);
        }

        // 3. Acesso negado - usuário não tem a função
        $this->logAccessDenied($user, $role, 'funcao_insuficiente', $request);
        return response()->json([
            'error' => 'Acesso negado',
            'message' => 'Você não possui a função necessária para acessar este recurso.'
        ], 403);
    }

    /**
     * Verifica se o usuário tem acesso total (admin/pastor)
     */
    private function hasFullAccess($user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'root', 'super_admin' ]);
    }

    /**
     * Obtém funções ativas e válidas do usuário
     */
    private function getFuncoesAtivas($user)
    {
        // Buscar membro do usuário
        $membro = $user->membros()->where('principal', true)->first();
        if (!$membro) {
            return collect();
        }

        return $user->membros()
            ->where('principal', true)
            ->first()
            ->membroFuncoes()
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->with('funcao')
            ->get();
    }

    /**
     * Verifica se as funções incluem a função necessária
     */
    private function verificarFuncao($funcoesAtivas, string $role, bool $requireAll): bool
    {
        $funcoesEncontradas = 0;
        $funcoesNecessarias = explode('|', $role); // Permite múltiplas funções separadas por |

        foreach ($funcoesAtivas as $membroFuncao) {
            $funcao = $membroFuncao->funcao;

            if ($funcao && in_array($funcao->nome, $funcoesNecessarias)) {
                $funcoesEncontradas++;

                if (!$requireAll) {
                    return true; // Se não requer todas, uma já basta
                }
            }
        }

        // Se requeria todas as funções, verificar se encontrou todas
        return $requireAll ? ($funcoesEncontradas === count($funcoesNecessarias)) : ($funcoesEncontradas > 0);
    }

    /**
     * Registra log de acesso administrativo
     */
    private function logAdminAccess($user, string $role, Request $request): void
    {
        IgrejaPermissaoLog::create([
            'igreja_id' => $user->getIgrejaId(),
            'acao' => 'acesso_administrativo_role',
            'detalhes' => [
                'role_solicitado' => $role,
                'role_usuario' => $user->role,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'realizado_por' => $user->id,
            'realizado_em' => now(),
        ]);
    }

    /**
     * Registra log de acesso negado
     */
    private function logAccessDenied($user, string $role, string $motivo, Request $request): void
    {
        $membro = $user->membros()->where('principal', true)->first();

        IgrejaPermissaoLog::create([
            'igreja_id' => $membro ? $membro->igreja_id : null,
            'membro_id' => $membro ? $membro->id : null,
            'acao' => 'acesso_negado_role',
            'detalhes' => [
                'role_solicitado' => $role,
                'motivo' => $motivo,
                'role_usuario' => $user->role,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'realizado_por' => $user->id,
            'realizado_em' => now(),
        ]);
    }

    /**
     * Registra log de acesso autorizado
     */
    private function logAccessGranted($user, string $role, Request $request): void
    {
        $membro = $user->membros()->where('principal', true)->first();

        IgrejaPermissaoLog::create([
            'igreja_id' => $membro ? $membro->igreja_id : null,
            'membro_id' => $membro ? $membro->id : null,
            'acao' => 'acesso_autorizado_role',
            'detalhes' => [
                'role_utilizado' => $role,
                'role_usuario' => $user->role,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'realizado_por' => $user->id,
            'realizado_em' => now(),
        ]);
    }
}
