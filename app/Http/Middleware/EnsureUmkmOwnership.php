<?php

namespace App\Http\Middleware;

use App\Models\Umkm;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUmkmOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get UMKM from route parameter (Laravel route model binding)
        $umkm = $request->route('umkm');
        
        // If there's no UMKM in route, allow the request to continue
        if (!$umkm) {
            return $next($request);
        }
        
        // If it's an ID (string/int), find the UMKM
        if (!$umkm instanceof Umkm) {
            $umkm = Umkm::find($umkm);
            
            // Check if UMKM exists
            if (!$umkm) {
                abort(404, 'UMKM not found');
            }
        }
        
        // Check if the authenticated user owns this UMKM
        if ($umkm->user_id !== Auth::id()) {
            abort(403, 'Unauthorized: You can only access your own UMKM');
        }
        
        return $next($request);
    }
}
