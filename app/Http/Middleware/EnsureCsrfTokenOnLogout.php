<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCsrfTokenOnLogout
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For logout requests, be more lenient with CSRF token validation
        // This helps prevent 419 errors on logout
        if ($request->path() === 'logout' && $request->isMethod('post')) {
            // Check if it's a valid authenticated session
            // Even if CSRF token might be slightly off, allow logout to proceed
            if (auth()->check()) {
                // Regenerate a fresh token for the logout request
                $request->session()->regenerateToken();
            }
        }

        return $next($request);
    }
}
