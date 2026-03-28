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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() < 500) {
                return; // don't log 404s and client errors here (unless wanted)   
            }
            if ($e instanceof \PDOException) {
                return; // avoid logging database connection errors to DB
            }
            try {
                \App\Helpers\SystemLogHelper::log('System Error', 'System', substr($e->getMessage(), 0, 500));
            } catch (\Throwable $err) {
                // fail silently
            }
        });
    })->create();

$app->register(\Barryvdh\DomPDF\ServiceProvider::class);
