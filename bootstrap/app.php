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
        //
        $middleware->trustProxies(
        '*', // Confía en cualquier proxy (como tenías)

        // Pero confía específicamente en ESTOS headers (el estándar)
        headers: Request::HEADER_X_FORWARDED_FOR |
                 Request::HEADER_X_FORWARDED_HOST |
                 Request::HEADER_X_FORWARDED_PORT |
                 Request::HEADER_X_FORWARDED_PROTO |
                 Request::HEADER_X_FORWARDED_AWS_ELB
    );

        // Middleware global para establecer término académico
        $middleware->web(append: [
            \App\Http\Middleware\SetCurrentTerm::class,
            \App\Http\Middleware\ValidateSession::class,
        ]);

        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckUserRol::class,
            'log' => \App\Http\Middleware\LogActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
