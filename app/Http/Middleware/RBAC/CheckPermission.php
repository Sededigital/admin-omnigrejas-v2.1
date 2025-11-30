<?php

namespace App\Http\Middleware\RBAC;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\RBAC\IgrejaMembroFuncao;
use App\Models\RBAC\IgrejaPermissaoLog;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission, string $requireAll = 'false'): Response
    {
        $user = Auth::user();

        // 1. Verificar se usuário está autenticado
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        // 2. Verificar se tem a permissão através do Gate
        if (Gate::allows($permission, $user)) {
            // Registrar log de acesso autorizado
            $this->logAccessGranted($user, $permission, $request);
            return $next($request);
        }

        // 3. Acesso negado - usuário não tem a permissão
        $this->logAccessDenied($user, $permission, 'permissao_insuficiente', $request);
        return response()->json([
            'error' => 'Acesso negado',
            'message' => 'Você não possui permissão para acessar este recurso.'
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

        return IgrejaMembroFuncao::where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->with(['funcao.permissoes'])
            ->get();
    }

    /**
     * Verifica se as funções têm a permissão necessária
     */
    private function verificarPermissao($funcoesAtivas, string $permission, bool $requireAll): bool
    {
        foreach ($funcoesAtivas as $membroFuncao) {
            $funcao = $membroFuncao->funcao;

            if ($funcao && $funcao->permissoes) {
                $temPermissao = $funcao->permissoes->contains(function($permissao) use ($permission) {
                    return $permissao->codigo === $permission && $permissao->ativo;
                });

                if ($temPermissao) {
                    if (!$requireAll) {
                        return true; // Se não requer todas, uma já basta
                    }
                    // Se requer todas, continua verificando
                } elseif ($requireAll) {
                    return false; // Se requer todas e não tem esta, falha
                }
            }
        }

        return $requireAll ? true : false; // Se chegou aqui e requeria todas, passou
    }

    /**
     * Registra log de acesso administrativo
     */
    private function logAdminAccess($user, string $permission, Request $request): void
    {
        IgrejaPermissaoLog::create([
            'igreja_id' => $user->getIgrejaId(),
            'acao' => 'acesso_administrativo',
            'detalhes' => [
                'permissao_solicitada' => $permission,
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
    private function logAccessDenied($user, string $permission, string $motivo, Request $request): void
    {
        $membro = $user->membros()->where('principal', true)->first();

        IgrejaPermissaoLog::create([
            'igreja_id' => $membro ? $membro->igreja_id : null,
            'membro_id' => $membro ? $membro->id : null,
            'acao' => 'acesso_negado',
            'detalhes' => [
                'permissao_solicitada' => $permission,
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
    private function logAccessGranted($user, string $permission, Request $request): void
    {
        $membro = $user->membros()->where('principal', true)->first();

        IgrejaPermissaoLog::create([
            'igreja_id' => $membro ? $membro->igreja_id : null,
            'membro_id' => $membro ? $membro->id : null,
            'acao' => 'acesso_autorizado',
            'detalhes' => [
                'permissao_utilizada' => $permission,
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
