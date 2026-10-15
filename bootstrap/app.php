<?php

use App\Http\Middleware\EnsurePartakerSessionExists;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'partaker.session' => EnsurePartakerSessionExists::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions){
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if ($response->getStatusCode() === 410) {
                return response()->view('errors.410', [
                    'expiredAt' => $e->getMessage(),
                ], 410);
            }

            return $response;
        });
    })->create();