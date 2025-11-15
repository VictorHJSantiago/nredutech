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
        
    })
    ->withProviders([
        ...in_array(env('APP_ENV'), ['local', 'testing', 'dusk'])
            ? [Laravel\Dusk\DuskServiceProvider::class]
            : [],
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        
    })->create();
