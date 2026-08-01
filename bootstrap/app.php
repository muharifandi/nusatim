<?php

use App\Http\Middleware\ComingSoonMode;
use App\Http\Middleware\EnsurePartnerApprovedApi;
use App\Http\Middleware\TrackPageView;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            ComingSoonMode::class,
            TrackPageView::class,
        ]);

        $middleware->alias([
            'partner.approved.api' => EnsurePartnerApprovedApi::class,
        ]);

        // Laravel's own default guest redirect (route('login')) crashes with
        // a RouteNotFoundException on any unauthenticated non-JSON request,
        // since this app has no route literally named 'login' (Filament
        // panels use their own route names). Harmless for browser/web
        // requests that were already broken this same way before api.php
        // existed, but api/* must never redirect - returning null here makes
        // Authenticate throw a plain AuthenticationException, which
        // shouldRenderJsonWhen() below turns into a clean 401 JSON response
        // instead of a redirect, regardless of the client's Accept header.
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('api/*') ? null : route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
