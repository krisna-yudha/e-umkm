<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'user') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $wishlists = Wishlist::where('user_id', $user->id)
            ->with(['umkm:id,nama_umkm,kategori,alamat,gambar,status'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'wishlists' => $wishlists,
            'count' => $wishlists->count(),
        ]);
    }

    /**
     * Add UMKM to wishlist
     */
    public function store(Request $request, Umkm $umkm)
    {
        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'user') {
            return response()->json(['error' => 'Hanya pengguna biasa yang dapat menambahkan ke wishlist'], 403);
        }

        // Check if already in wishlist
        $existing = Wishlist::where('umkm_id', $umkm->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Sudah dalam wishlist'], 200);
        }

        // Add to wishlist
        $wishlist = Wishlist::create([
            'umkm_id' => $umkm->id,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Berhasil ditambahkan ke wishlist',
            'wishlist' => $wishlist,
        ], 201);
    }

    /**
     * Remove from wishlist
     */
    public function destroy(Umkm $umkm)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $wishlist = Wishlist::where('umkm_id', $umkm->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$wishlist) {
            return response()->json(['error' => 'Tidak ditemukan di wishlist'], 404);
        }

        $wishlist->delete();

        return response()->json(['message' => 'Berhasil dihapus dari wishlist']);
    }

    /**
     * Check if UMKM is in user's wishlist
     */
    public function check(Umkm $umkm)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['wishlisted' => false]);
        }

        $wishlisted = Wishlist::where('umkm_id', $umkm->id)
            ->where('user_id', $user->id)
            ->exists();

        return response()->json(['wishlisted' => $wishlisted]);
    }
}
