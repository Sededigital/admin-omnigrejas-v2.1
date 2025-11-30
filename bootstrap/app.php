<?php

use App\Providers\RBACServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Substituir o middleware CSRF padrão pelo nosso customizado
        $middleware->replace(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \App\Http\Middleware\VerifyCsrfToken::class);

        $middleware->alias([
                '2fa' => \App\Http\Middleware\EnsureTwoFactorIsEnabled::class,
                'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
                'isSuperAdmin' => \App\Http\Middleware\isSuperAdmin::class,
                'isAdminIgreja' => \App\Http\Middleware\isAdminIgreja::class,
                'isRoot' => \App\Http\Middleware\isRoot::class,
                '2fa-challenge'=> \App\Http\Middleware\EnsureTwoFactorChallengeEnabled::class,
                'checkSelectedChurch' => \App\Http\Middleware\CheckSelectedChurch::class,
                'checkUserStatus' => \App\Http\Middleware\CheckUserStatus::class,
                'checkSubscription' => \App\Http\Middleware\CheckSubscription::class,

                // Middlewares RBAC - Sistema de Permissões
                'permission' => \App\Http\Middleware\RBAC\CheckPermission::class,
                'role' => \App\Http\Middleware\RBAC\CheckRole::class,
                'hasFunction' => \App\Http\Middleware\RBAC\CheckFunction::class,
                'checkValidPermissions' => \App\Http\Middleware\RBAC\CheckValidPermissions::class,

            ]);


    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        RBACServiceProvider::class,
    ])
    ->create();
