<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'v1/api',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // $middleware->validateCsrfTokens(except: [
    
        // ]);
    
        // $middleware->api([
        //     EnsureFrontendRequestsAreStateful::class,
        //     StartSession::class,
        //     VerifyCsrfToken::class,
        //     SubstituteBindings::class,
        // ]);
    
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
    
        // $middleware->web([
        //     StartSession::class,
        //     ShareErrorsFromSession::class,
        //     VerifyCsrfToken::class,
        //     SubstituteBindings::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

