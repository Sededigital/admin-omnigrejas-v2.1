<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Igrejas\IgrejaMembro;

class CheckSelectedChurch
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Super Admin e Root têm acesso global, passam direto
        if ($user && in_array($user->role, ['super_admin', 'root'])) {
            return $next($request);
        }

        // Para usuários normais
        if ($user) {
            $igrejas = IgrejaMembro::where('user_id', $user->id)
                ->where('status', 'ativo')
                ->with('igreja')
                ->get();

            // Se não tem igrejas ativas, redirecionar para dashboard apropriado com warning
            if ($igrejas->isEmpty()) {
                $dashboardRoute = match($user->role) {
                    'super_admin' => 'dashboard.administrative',
                    'admin', 'pastor', 'ministro'  => 'dashboard-admin.church',
                    'membro', 'diacono', 'obreiro' => 'dashboard.member',
                    default => 'dashboard.member'
                };
                return redirect()->route($dashboardRoute)->with('warning', 'Você ainda não pertence a nenhuma igreja.');
            }

            // Se tem apenas uma igreja, verificar se tem código de acesso
            if ($igrejas->count() === 1) {
                $igreja = $igrejas->first()->igreja;

                // Se a igreja tem código de acesso E a sessão não está definida, forçar seleção
                if (!empty($igreja->code_access) && !session()->has('igreja_atual')) {
                    return $this->checkSessionTimeout($request, 'selecionar.igreja');
                }

                // Se não tem código de acesso OU a sessão já está definida, prosseguir
                if (empty($igreja->code_access) || session()->has('igreja_atual')) {
                    // Garantir que a sessão está definida
                    if (!session()->has('igreja_atual')) {
                        session(['igreja_atual' => $igreja]);
                    }
                    return $next($request);
                }
            }

            // Se tem múltiplas igrejas, verificar se já selecionou
            if (!session()->has('igreja_atual')) {
                return $this->checkSessionTimeout($request, 'selecionar.igreja');
            }
        }

        return $next($request);
    }

    /**
     * Verifica se a sessão expirou (mais de 2 minutos sem seleção de igreja)
     */
    private function checkSessionTimeout(Request $request, string $redirectRoute)
    {
        $sessionStart = session('church_selection_start');

        // Se não tem timestamp de início, definir agora
        if (!$sessionStart) {
            session(['church_selection_start' => now()->timestamp]);
            return redirect()->route($redirectRoute);
        }

        // Verificar se passaram mais de 2 minutos (120 segundos)
        $elapsed = now()->timestamp - $sessionStart;
        if ($elapsed > 120) {
            // Sessão expirou - fazer logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'Sua sessão expirou por inatividade. Faça login novamente.');
        }

        return redirect()->route($redirectRoute);
    }
}
