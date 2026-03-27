<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Billings\AssinaturaAtual;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Super Admin e Root têm acesso ilimitado
        if ($user && in_array($user->role, ['super_admin', 'root'])) {
            return $next($request);
        }

        // Verificar se é uma rota do UpgradePage (subscription.upgrade)
        $isUpgradePage = $request->route() && str_contains($request->route()->getName(), 'subscription.upgrade');

        // Se for UpgradePage, permitir acesso para todos (logados e não logados)
        // As ações serão controladas no componente Livewire
        if ($isUpgradePage) {
            return $next($request);
        }

        // Verificar se usuário tem igreja selecionada
        $igrejaAtual = session('igreja_atual');
        if (!$igrejaAtual) {
            return $next($request); // Deixar CheckSelectedChurch lidar com isso
        }

        // Verificar se é usuário trial
        if ($user && $user->role === 'admin' && $user->trial) {
            // Usar TrialService para verificar acesso
            $trialService = app(\App\Services\Trial\TrialService::class);
            if (!$trialService->usuarioPodeAcessar($user)) {
                // Redirecionar para página de upgrade com mensagem específica
                return redirect()->route('ecommerce.subscription.upgrade', $igrejaAtual->id)
                    ->with('warning', $trialService->getMensagemBloqueio($user));
            }
            // Se trial está ativo, permitir acesso
            return $next($request);
        }

        // Verificar assinatura da igreja para usuários não-trial
        $cacheKey = "assinatura_atual_{$igrejaAtual->id}";
        $assinatura = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($igrejaAtual) {
            return AssinaturaAtual::where('igreja_id', $igrejaAtual->id)->first();
        });

        // Se não tem assinatura OU assinatura expirou
        if (!$assinatura || !$assinatura->estaAtiva()) {
            // Redirecionar para página de upgrade
            return redirect()->route('ecommerce.subscription.upgrade', $igrejaAtual->id)
                ->with('warning', 'Sua assinatura expirou ou não foi encontrada.');
        }

        return $next($request);
    }
}