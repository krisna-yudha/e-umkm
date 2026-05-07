<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugCsrfToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log CSRF token info for debugging
        if ($request->isMethod('post')) {
            \Illuminate\Support\Facades\Log::debug('CSRF Debug', [
                'path' => $request->path(),
                'method' => $request->method(),
                'token_from_request' => substr($request->input('_token') ?? $request->header('X-CSRF-TOKEN') ?? 'none', 0, 20),
                'token_from_session' => substr($request->session()->token() ?? 'none', 0, 20),
                'session_id' => $request->session()->getId(),
            ]);
        }

        return $next($request);
    }
}
