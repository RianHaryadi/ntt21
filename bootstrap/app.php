<?php

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
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SetCurrency::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Lapor semua exception tak tertangani ke Sentry.
        // Nonaktif selama SENTRY_LARAVEL_DSN belum diisi di .env.
        \Sentry\Laravel\Integration::handles($exceptions);
    })->create();
