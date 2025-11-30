<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ThrottleLoginAttempts
{
    public function handle(Request $request, Closure $next)
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        RateLimiter::hit($key, $this->decayMinutes() * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $this->maxAttempts(),
            $this->calculateRemainingAttempts($key)
        );
    }

    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(implode('|', [
            $request->method(),
            $request->route()->getDomain(),
            $request->path(),
            $request->ip(),
            $request->userAgent(),
        ]));
    }

    protected function maxAttempts(): int
    {
        return 5; // 5 tentativas
    }

    protected function decayMinutes(): int
    {
        return 15; // Bloqueio por 15 minutos
    }

    protected function calculateRemainingAttempts(string $key): int
    {
        return RateLimiter::remaining($key, $this->maxAttempts());
    }

    protected function addHeaders($response, int $maxAttempts, int $remainingAttempts): mixed
    {
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);
    }
}
