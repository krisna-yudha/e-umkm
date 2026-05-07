<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCsrfTokenHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            // Set CSRF token in response headers so axios can update it
            // Use csrf_token() which works even after session invalidation
            $token = csrf_token();
            if ($token) {
                $response->headers->set('X-CSRF-TOKEN', $token);
            }
        } catch (\Exception $e) {
            // If CSRF token generation fails, continue without setting it
            // This can happen during logout when session is being destroyed
        }

        return $response;
    }
}
