<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Se não há usuário autenticado, prosseguir (outros middlewares vão lidar)
        if (!$user) {
            return $next($request);
        }

        // Verificar se o status do usuário permite acesso
        if ($user->status !== 'ativo') {
            // Fazer logout do usuário
            Auth::logout();

            // Invalidar a sessão
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirecionar para login com mensagem
            return redirect()->route('login')->with([
                'error' => 'Sua conta foi suspensa ou bloqueada. Entre em contato com o administrador.',
                'type' => 'danger'
            ]);
        }

        return $next($request);
    }
}
