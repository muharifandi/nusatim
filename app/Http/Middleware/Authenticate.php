<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * This app has no route literally named 'login' (Filament panels use
     * their own route names, e.g. filament.admin.auth.login) - the base
     * Laravel behavior of calling route('login') here would throw a
     * RouteNotFoundException for any unauthenticated non-JSON request.
     * api/* must never attempt this redirect at all (mobile clients aren't
     * guaranteed to send Accept: application/json) - returning null lets
     * Handler::shouldReturnJson() render a clean 401 JSON response instead.
     * Non-api routes keep the same pre-existing gap (out of scope here,
     * not a regression introduced by this migration).
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('api/*')) {
            return null;
        }

        return $request->expectsJson() ? null : route('login');
    }
}
