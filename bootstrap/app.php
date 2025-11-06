<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias middleware Anda di sini
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'guru' => \App\Http\Middleware\IsGuru::class,
            'siswa' => \App\Http\Middleware\IsSiswa::class,
            'role'    => \App\Http\Middleware\RoleMiddleware::class, //
        ]);
        $middleware->trustProxies('*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
