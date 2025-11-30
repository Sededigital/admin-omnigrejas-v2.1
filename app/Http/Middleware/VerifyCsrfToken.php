<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Log do erro CSRF personalizado
            \Illuminate\Support\Facades\Log::warning('CSRF Token Mismatch - Custom Handler', [
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
                'timestamp' => now()->toISOString(),
                'exception_message' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
            ]);

            // Se for uma requisição AJAX/Livewire, retornar resposta JSON
            if ($request->expectsJson() || $request->is('livewire/*')) {
                return response()->json([
                    'csrf_expired' => true,
                    'message' => 'Sessão expirada. Recarregando página...',
                    'redirect' => $request->fullUrl()
                ], 419);
            }

            // Para requisições normais, redirecionar de volta com mensagem
            return redirect()->back()->with([
                'csrf_expired' => true,
                'message' => 'Sua sessão expirou. A página será recarregada.',
                'type' => 'warning'
            ]);
        }
    }
}
