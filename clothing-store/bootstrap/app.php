<?php

use App\Http\Middleware\CaptureUtmParameters;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->web(append: \App\Http\Middleware\CaptureUtmParameters::class);
        $middleware->web(prepend: \Illuminate\Cookie\Middleware\EncryptCookies::class);
        $middleware->web(prepend: \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class);
        $middleware->web(append: \Illuminate\Session\Middleware\StartSession::class);
        $middleware->web(append: \Illuminate\View\Middleware\ShareErrorsFromSession::class);
        $middleware->web(append: \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $middleware->web(append: \Illuminate\Routing\Middleware\SubstituteBindings::class);
        $middleware->alias([
            'admin.idle-timeout' => \App\Http\Middleware\AdminIdleTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withEvents(true)->create();
