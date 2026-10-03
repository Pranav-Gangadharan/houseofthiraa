<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render terminates TLS in front of nginx; trust its X-Forwarded-* headers so
        // generated URLs (Vite assets, links) use https instead of being blocked as mixed content.
        $middleware->trustProxies(at: '*');

        // Razorpay calls this server-to-server; it authenticates with its own signature.
        $middleware->validateCsrfTokens(except: ['razorpay/webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
