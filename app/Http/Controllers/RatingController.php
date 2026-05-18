<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RatingController extends Controller
{
    /**
     * Store a new rating/review for an UMKM
     */
    public function store(Request $request, Umkm $umkm)
    {
        // Comprehensive debug logging
        $sessionUserType = Session::get('user_type', 'user');
        $sessionUserId = Session::get('user_id');
        $sessionData = Session::all();
        $authCheck = Auth::check();
        $authUser = Auth::user();
        
        Log::info('=== RATING STORE REQUEST - DETAILED DEBUG ===', [
            'timestamp' => now(),
            'url' => $request->url(),
            'method' => $request->method(),
            'umkm_id' => $umkm->id,
        ]);

        Log::info('AUTH STATUS', [
            'auth_check' => $authCheck,
            'auth_id' => Auth::id(),
            'auth_guard' => Auth::getDefaultDriver(),
            'auth_user_id' => $authUser?->id,
            'auth_user_name' => $authUser?->name,
            'auth_user_type' => $authUser?->user_type,
        ]);

        Log::info('SESSION STATUS', [
            'session_id' => Session::getId(),
            'session_exists' => Session::has('user_id'),
            'session_user_id' => $sessionUserId,
            'session_user_type' => $sessionUserType,
            'session_all_keys' => array_keys($sessionData),
        ]);

        Log::info('REQUEST HEADERS', [
            'has_csrf_token' => $request->header('X-CSRF-TOKEN') ? 'YES' : 'NO',
            'has_cookie_header' => $request->header('Cookie') ? 'YES' : 'NO',
            'laravel_session_cookie' => $request->cookie('LARAVEL_SESSION') ? substr($request->cookie('LARAVEL_SESSION'), 0, 20) . '...' : 'MISSING',
        ]);

        // Check authentication
        if (!$authCheck) {
            Log::error('❌ AUTHENTICATION FAILED - Auth::check() returned false', [
                'session_id' => Session::getId(),
                'session_has_user_id' => Session::has('user_id'),
            ]);
            
            return response()->json([
                'error' => 'Sesi Anda telah berakhir. Silakan login terlebih dahulu',
                'debug' => [
                    'auth_check' => false,
                    'session_id' => Session::getId(),
                    'session_has_user' => Session::has('user_id'),
                ]
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = $authUser;
        $userType = $user->user_type ?? 'user';
        
        Log::info('✅ USER AUTHENTICATED', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_type' => $userType,
        ]);
        
        // Check if user is trying to rate their own UMKM
        if ($umkm->isOwnedBy($user->id)) {
            Log::warning('❌ UMKM OWNER TRIED TO RATE OWN UMKM', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'umkm_id' => $umkm->id,
            ]);
            return response()->json([
                'error' => 'Anda tidak dapat memberikan rating untuk UMKM Anda sendiri'
            ], 403);
        }

        // Validate input
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ VALIDATION FAILED', $e->errors());
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }

        // Check if user already rated
        $existingRating = Rating::where('umkm_id', $umkm->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingRating) {
            $existingRating->update($validated);
            Log::info('✅ RATING UPDATED', [
                'rating_id' => $existingRating->id,
                'user_id' => $user->id,
                'umkm_id' => $umkm->id,
                'new_rating' => $validated['rating'],
            ]);
            return response()->json([
                'message' => 'Rating berhasil diperbarui',
                'rating' => $existingRating,
            ]);
        }

        // Create new rating
        try {
            $rating = Rating::create([
                'umkm_id' => $umkm->id,
                'user_id' => $user->id,
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? null,
            ]);

            Log::info('✅ NEW RATING CREATED', [
                'rating_id' => $rating->id,
                'user_id' => $user->id,
                'umkm_id' => $umkm->id,
                'rating_value' => $validated['rating'],
                'has_review' => !empty($validated['review']),
            ]);

            return response()->json([
                'message' => 'Rating berhasil ditambahkan',
                'rating' => $rating->load('user'),
            ], 201);
        } catch (\Exception $e) {
            Log::error('❌ ERROR CREATING RATING', [
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'user_id' => $user->id,
                'umkm_id' => $umkm->id,
            ]);
            return response()->json([
                'error' => 'Gagal menyimpan rating: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get ratings for an UMKM
     */
    public function index(Umkm $umkm)
    {
        $ratings = Rating::where('umkm_id', $umkm->id)
            ->with('user:id,name,profile_photo')
            ->orderByDesc('created_at')
            ->get();

        $averageRating = $ratings->avg('rating') ?? 0;
        $totalRatings = $ratings->count();

        // Get distribution of ratings
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $ratings->where('rating', $i)->count();
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $totalRatings > 0 ? round(($count / $totalRatings) * 100) : 0,
            ];
        }

        return response()->json([
            'ratings' => $ratings,
            'average_rating' => round($averageRating, 1),
            'total_ratings' => $totalRatings,
            'distribution' => $distribution,
        ]);
    }

    /**
     * Delete a rating
     */
    public function destroy(Rating $rating)
    {
        // Ensure user can only delete their own ratings or if they're admin
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (Auth::id() !== $rating->user_id && !$authUser->isAdmin()) {
            return response()->json(['error' => 'Anda tidak memiliki izin untuk menghapus rating ini'], 403);
        }

        $rating->delete();
        return response()->json(['message' => 'Rating berhasil dihapus']);
    }

    /**
     * Mark rating as helpful
     */
    public function markHelpful(Rating $rating)
    {
        // Increment helpful count
        $rating->increment('helpful_count');
        
        return response()->json([
            'message' => 'Terima kasih atas masukan Anda',
            'helpful_count' => $rating->helpful_count,
        ]);
    }

    /**
     * Get current user's ratings
     */
    public function userRatings(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ratings' => [], 'message' => 'Unauthorized'], 401);
        }

        $ratings = Rating::where('user_id', Auth::id())
            ->with('umkm:id,nama_umkm,kategori,gambar')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'ratings' => $ratings,
            'count' => $ratings->count(),
        ]);
    }
}
