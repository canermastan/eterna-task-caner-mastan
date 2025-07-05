<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                throw new \Illuminate\Auth\AuthenticationException();
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API exception handling - JSON responses for API routes
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return match (true) {
                    $e instanceof ValidationException => response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $e->errors(),
                    ], 422),
                    
                    $e instanceof AuthenticationException => response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated',
                    ], 401),
                    
                    $e instanceof NotFoundHttpException => response()->json([
                        'success' => false,
                        'message' => 'Resource not found',
                    ], 404),
                    
                    default => response()->json([
                        'success' => false,
                        'message' => app()->hasDebugModeEnabled() ? $e->getMessage() : 'Internal server error',
                        'error' => app()->hasDebugModeEnabled() ? [
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString(),
                        ] : null,
                    ], 500),
                };
            }
        });
    })->create();
