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
        // Register middleware aliases
        $middleware->alias([
            'role'      => \App\Http\Middleware\Role::class,     // <-- ADD THIS
            'admin'     => \App\Http\Middleware\IsAdmin::class,  // (optional, if you use it)
            'seller'    => \App\Http\Middleware\IsSeller::class, // (optional, if you use it)
            'admin.api' => \App\Http\Middleware\AdminApiMiddleware::class,
        ]);

        // $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
