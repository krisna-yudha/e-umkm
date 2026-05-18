<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Authenticate user via session (web guard) for API requests
 * Specifically for endpoints that need session-based auth (ratings, wishlist)
 */
class AuthenticateWithSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get session ID for debugging
        $sessionId = session()->getId();
        $cookies = $request->cookie();
        $laravel_session_cookie = $cookies['LARAVEL_SESSION'] ?? 'NOT_FOUND';
        
        // Log initial state
        Log::debug('AuthenticateWithSession: Initial state', [
            'path' => $request->path(),
            'method' => $request->method(),
            'session_id' => $sessionId,
            'cookie_LARAVEL_SESSION' => substr($laravel_session_cookie, 0, 20) . '...',
            'all_cookies' => array_keys($cookies),
        ]);

        // Try to authenticate with web guard
        $authCheck = Auth::guard('web')->check();
        $user = Auth::user();
        
        Log::debug('AuthenticateWithSession: Auth status', [
            'auth_check' => $authCheck,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_type' => $user?->user_type,
            'session_data_keys' => array_keys(session()->all()),
        ]);

        if (!$authCheck) {
            Log::warning('AuthenticateWithSession: User not authenticated', [
                'path' => $request->path(),
                'method' => $request->method(),
                'session_id' => $sessionId,
                'session_has_data' => !empty(session()->all()),
                'laravel_session_cookie_present' => $laravel_session_cookie !== 'NOT_FOUND',
            ]);

            return response()->json([
                'error' => 'Sesi Anda telah berakhir. Silakan login terlebih dahulu',
                'debug' => [
                    'auth_check' => false,
                    'session_id' => $sessionId,
                    'session_empty' => empty(session()->all()),
                    'cookie_present' => $laravel_session_cookie !== 'NOT_FOUND',
                ]
            ], 401);
        }

        Log::info('AuthenticateWithSession: User authenticated', [
            'path' => $request->path(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_type' => $user->user_type,
        ]);

        return $next($request);
    }
}

