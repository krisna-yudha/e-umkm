<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUmkmType
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is not UMKM, redirect to dashboard
        if ($user && $user->user_type !== 'umkm') {
            abort(403, 'Hanya pemilik UMKM yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
