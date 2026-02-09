<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware as ConfigMiddleware;
use App\Http\Middleware\ParseJsonBody;
use App\Http\Middleware\TrackVisitor;
use App\Http\Middleware\ForceHttpsInProduction;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\BlockIp;
use App\Http\Middleware\CustomThrottleRequests;
use Inertia\Middleware as InertiaMiddleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\AppServiceProvider::class,
    ])
    ->withMiddleware(function (ConfigMiddleware $middleware) {
        // Ensure CORS headers are set before other middleware.
        $middleware->prepend(HandleCors::class);
        $middleware->append(BlockIp::class);
        // Force HTTPS in production
        $middleware->append(ForceHttpsInProduction::class);
        
        // Ensure JSON request bodies are parsed even when PHP input is empty (PowerShell/curl quirks)
        $middleware->append(ParseJsonBody::class);
        
        // Track visitor activity on web routes
        $middleware->web(append: [
            TrackVisitor::class,
            InertiaMiddleware::class,
        ]);

        // Enable stateful SPA auth for Sanctum
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Register middleware aliases
        $middleware->alias([
            'role' => CheckRole::class,
            'throttle' => CustomThrottleRequests::class,
        ]);
        
        // Redirect unauthenticated users to the appropriate login page
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
