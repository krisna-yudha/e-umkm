<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add session middleware to API routes for Sanctum session-based auth
        $middleware->api(append: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Prepend middleware to run BEFORE CSRF validation
        $middleware->web(prepend: [
            \App\Http\Middleware\EnsureCsrfTokenOnLogout::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetCsrfTokenHeader::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Skip CSRF validation for API routes using callback
        $middleware->validateCsrfTokens(except: [
            'api/*',
            '/api/*',
            'logout',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'umkm.owner' => \App\Http\Middleware\EnsureUmkmOwnership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
