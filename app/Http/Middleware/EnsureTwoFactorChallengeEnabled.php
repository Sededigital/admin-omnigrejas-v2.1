<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TwoFactorHelper;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorChallengeEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !TwoFactorHelper::isEnabled($user)) {
            return redirect()->route('login')->with('error', 'Autenticação de dois fatores não está ativada.');
        }

        return $next($request);
    }
}
