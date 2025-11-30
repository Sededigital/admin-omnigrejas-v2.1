<?php

namespace App\Http\Middleware\RBAC;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\RBAC\PermissionHelper;
use Symfony\Component\HttpFoundation\Response;

class CheckValidPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        # 1. Verificar se usuário está autenticado
        if (!$user) {
            //**/ Log::info('CheckValidPermissions: Usuário não autenticado, prosseguindo', [
            //**/     'url' => $request->fullUrl(),
            //**/     'ip' => $request->ip(),
            //**/ ]);
            return $next($request);
        } 

        //** Buscar primeiro membro ativo do usuário
        //**  $membro = $user->membros()->where('status', 'ativo')->first();

        //**/ Log::info('CheckValidPermissions: Iniciando verificação', [
        //**/     'user_id' => $user->id,
        //**/     'user_email' => $user->email,
        //**/     'user_role' => $user->role,
        //**/     'membro_cargo' => $membro ? $membro->cargo : 'sem_membro',
        //**/     'url' => $request->fullUrl(),
        //**/     'session_igreja_atual' => session('igreja_atual.id') ?? 'null',
        //**/ ]);

        // 2. Verificar se usuário tem permissões válidas
        $hasValidPermissions = $this->checkUserHasValidPermissions($user);

        //**/ Log::info('CheckValidPermissions: Resultado da verificação', [
        //**/     'user_id' => $user->id,
        //**/     'hasValidPermissions' => $hasValidPermissions,
        //**/ ]);

        if (!$hasValidPermissions) {
            # Log da perda de permissões
            //**/ Log::warning('CheckValidPermissions: Usuário perdeu permissões válidas - fazendo logout automático', [
            //**/     'user_id' => $user->id,
            //**/     'user_email' => $user->email,
            //**/     'user_role' => $user->role,
            //**/     'url' => $request->fullUrl(),
            //**/     'ip' => $request->ip(),
            //**/     'user_agent' => $request->userAgent(),
            //**/     'session_igreja_atual' => session('igreja_atual.id') ?? 'null',
            //**/ ]);

            // Fazer logout
            Auth::logout();

            // Invalidar sessão
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirecionar para login com mensagem
            return redirect('/login')->with('error', 'Suas permissões foram revogadas. Faça login novamente.');
        }

        //**/ Log::info('CheckValidPermissions: Verificação passou, prosseguindo', [
        //**/     'user_id' => $user->id,
        //**/     'url' => $request->fullUrl(),
        //**/ ]);

        return $next($request);
    }

    /**
     * Verifica se o usuário tem permissões válidas
     */
    private function checkUserHasValidPermissions($user): bool
    {
        # Usuários com roles administrativos sempre têm acesso
        if (PermissionHelper::hasFullAccess($user)) {
            return true;
        }

        # Verificar se usuário tem pelo menos um membro ativo
        $hasActiveMember = $user->membros()->where('status', 'ativo')->exists();
        if (!$hasActiveMember) {
            //**/ Log::warning('Usuário não tem membro ativo', [
            //**/     'user_id' => $user->id,
            //**/     'user_email' => $user->email,
            //**/     'user_role' => $user->role,
            //**/ ]);
            return false;
        }

        # Verificar se usuário tem pelo menos uma função ativa
        $hasRole = PermissionHelper::hasAnyRole($user, false); # false = não usar cache para verificação em tempo real
        if (!$hasRole) {
            //**/ Log::warning('Usuário não tem funções ativas', [
            //**/     'user_id' => $user->id,
            //**/     'user_email' => $user->email,
            //**/     'user_role' => $user->role,
            //**/ ]);
            return false;
        }

        return true;
    }
}
