<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*'));

        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Los datos enviados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $message = $e->getPrevious() instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                ? 'El recurso solicitado no existe.'
                : ($e->getMessage() ?: 'Ocurrió un error al procesar la solicitud.');

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => [],
            ], $e->getStatusCode());
        });
    })->create();
