<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    /**
     * Store a new rating/review for an UMKM
     */
    public function store(Request $request, Umkm $umkm)
    {
        // Debug logging
        Log::info('=== RATING STORE REQUEST ===', [
            'url' => $request->url(),
            'method' => $request->method(),
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'null',
            'user_type' => Auth::user()?->user_type ?? 'null',
        ]);

        // Validate that user is logged in and is a regular user (not UMKM owner)
        if (!Auth::check()) {
            Log::warning('❌ Auth::check() failed - user not authenticated');
            return response()->json(['error' => 'Anda harus login terlebih dahulu', 'debug' => 'Auth::check() = false'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::info('✅ User authenticated:', ['user_id' => $user->id, 'name' => $user->name, 'user_type' => $user->user_type]);
        
        // REJECT hanya jika user adalah UMKM owner
        // ALLOW jika: user_type = 'user' ATAU user_type = NULL (old users) ATAU admin
        if ($user->user_type === 'umkm') {
            return response()->json(['error' => 'UMKM owner tidak dapat memberikan rating'], 403);
        }

        // Validate input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        // Check if user already rated this UMKM
        $existingRating = Rating::where('umkm_id', $umkm->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update($validated);
            return response()->json([
                'message' => 'Rating berhasil diperbarui',
                'rating' => $existingRating,
            ]);
        }

        // Create new rating
        $rating = Rating::create([
            'umkm_id' => $umkm->id,
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        return response()->json([
            'message' => 'Rating berhasil ditambahkan',
            'rating' => $rating->load('user'),
        ], 201);
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
}
