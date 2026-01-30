<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ParseJsonBody;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ensure JSON request bodies are parsed even when PHP input is empty (PowerShell/curl quirks)
        $middleware->append(ParseJsonBody::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
