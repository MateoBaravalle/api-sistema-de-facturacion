<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->is('api/*')) {
            return $this->unauthenticated($request, []);
        }

        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        abort(401, 'No autenticado');
    }
}
