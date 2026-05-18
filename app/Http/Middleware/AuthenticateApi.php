<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class AuthenticateApi extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle an incoming request - support both session and sanctum auth.
     */
    public function handle($request, ...$guards)
    {
        // Try session auth first (for web-based requests with cookies)
        if (!Auth::guard('web')->check()) {
            // If session auth fails, try sanctum auth
            if (!Auth::guard('sanctum')->check()) {
                return $this->unauthenticated($request, $guards ?? ['sanctum']);
            }
        }

        return $this->authenticate($request, $guards ?? ['web', 'sanctum']);
    }
}
