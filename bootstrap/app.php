<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            return $request->is('admin/*')
                ? route('admin.login')
                : route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

// On Vercel / Serverless environments, redirect storage to writable /tmp
if (getenv('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || is_dir('/tmp')) {
    $storagePath = '/tmp/storage';
    $dirs = [
        $storagePath . '/logs',
        $storagePath . '/framework/views',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/sessions',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
    $app->useStoragePath($storagePath);
}

return $app;
