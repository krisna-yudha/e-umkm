<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserContributionController extends Controller
{
    /**
     * Get all ratings by authenticated user
     */
    public function getUserRatings()
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ratings = Rating::where('user_id', $user->id)
            ->with(['umkm:id,nama_umkm,kategori,gambar'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'ratings' => $ratings,
            'count' => $ratings->count()
        ]);
    }

    /**
     * Get all wishlist items by authenticated user
     */
    public function getUserWishlist()
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $wishlist = Wishlist::where('user_id', $user->id)
            ->with(['umkm:id,nama_umkm,kategori,gambar,alamat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'wishlist' => $wishlist,
            'count' => $wishlist->count()
        ]);
    }

    /**
     * Get user contribution statistics
     */
    public function getContributionStats()
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ratingsCount = Rating::where('user_id', $user->id)->count();
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        $averageRating = Rating::where('user_id', $user->id)->avg('rating');
        $totalHelpful = Rating::where('user_id', $user->id)->sum('helpful_count');

        return response()->json([
            'ratings_count' => $ratingsCount,
            'wishlist_count' => $wishlistCount,
            'average_rating' => round($averageRating, 1),
            'total_helpful' => $totalHelpful,
            'total_contributions' => $ratingsCount + $wishlistCount
        ]);
    }
}
