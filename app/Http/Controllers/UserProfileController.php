<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            
            // Debug: Check if user exists and has umkms relation
            if (!$user) {
                abort(401, 'User not authenticated');
            }
            
            // Get user's UMKM data with menus
            $umkms = $user->umkms()
                ->with(['menus' => function($query) {
                    $query->where('tersedia', true)->orderBy('nama_menu');
                }])
                ->latest()
                ->get();
            
            return Inertia::render('Profile/UserProfile', [
                'umkms' => $umkms
            ]);
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('UserProfile Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return with empty umkms to prevent error
            return Inertia::render('Profile/UserProfile', [
                'umkms' => collect([])
            ]);
        }
    }
}