<?php

use App\Http\Middleware\NegotiateMarkdown;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->removeFromGroup('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
        ]);
        $middleware->appendToGroup('web', NegotiateMarkdown::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            $accept = (string) $request->header('Accept', '*/*');

            if (
                ! $request->isMethod('GET')
                || $request->is('api/*')
                || $request->expectsJson()
                || (str_contains($accept, 'text/html') && ! str_contains($accept, 'text/markdown'))
            ) {
                return null;
            }

            return response()
                ->view('errors.404-markdown', status: 404)
                ->header('Content-Type', 'text/markdown; charset=UTF-8')
                ->header('Vary', 'Accept, Accept-Encoding');
        });
    })->create();
