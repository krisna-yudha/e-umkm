<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * Verify CSRF token for session-based API requests
 * Skip verification for public/exempt routes
 */
class VerifyCsrfTokenForApi
{
    /**
     * Routes that should be exempted from CSRF verification
     */
    protected $except = [
        'api/v1/auth/*',
        'api/v1/umkm/*',
        'api/v1/umkms/*',      // Rating endpoints use plural
        'api/v1/menus/*',
        'api/v1/map/*',
        'api/v1/ratings/*',
        'api/v1/wishlist/*',
        'api/health',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF check for exempted routes
        if ($this->shouldSkip($request)) {
            Log::debug('VerifyCsrfTokenForApi: Skipping CSRF check for ' . $request->path());
            return $next($request);
        }

        // For non-exempted routes, verify CSRF token
        $token = $request->input('_token') ?? 
                 $request->header('X-CSRF-TOKEN') ?? 
                 $request->header('X-XSRF-TOKEN');

        $sessionToken = $request->session()->token();

        Log::debug('VerifyCsrfTokenForApi checking token', [
            'path' => $request->path(),
            'has_token' => !!$token,
            'has_session_token' => !!$sessionToken,
        ]);

        if (!$token || !$sessionToken || $token !== $sessionToken) {
            return response()->json([
                'error' => 'CSRF token mismatch', 
                'debug' => [
                    'request_path' => $request->path(),
                    'token_present' => !!$token,
                    'session_token_present' => !!$sessionToken,
                ]
            ], 419);
        }

        return $next($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return true;
            }
        }
        return false;
    }
}

