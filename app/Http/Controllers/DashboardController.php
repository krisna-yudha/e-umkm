<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function home()
    {
        return Inertia::render('Home', [
            'auth' => [
                'user' => Auth::user()
            ]
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        
        // Redirect admin users to admin dashboard
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => $user
            ]
        ]);
    }

    public function mapping()
    {
        // Get all active UMKM with location data for mapping
        $umkms = Umkm::with('user')
                    ->where('status', 'active')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->get();
        
        return Inertia::render('Mapping', [
            'umkms' => $umkms,
            'auth' => [
                'user' => Auth::user()
            ]
        ]);
    }

    public function publicUmkmList()
    {
        try {
            // Get UMKM data with pagination
            $umkms = Umkm::select([
                'id', 'nama_umkm', 'kategori', 'alamat', 'deskripsi', 
                'no_telepon', 'email', 'user_id', 'gambar', 'status'
            ])
            ->with([
                'user:id,name',
                'menus' => function($query) {
                    $query->where('tersedia', true)
                          ->select('id', 'umkm_id', 'nama_menu', 'harga', 'kategori_menu')
                          ->orderBy('nama_menu');
                }
            ])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12); // 12 UMKM per halaman
                
        } catch (\Exception $e) {
            // If database fails, return empty pagination with proper structure
            $umkms = new \Illuminate\Pagination\LengthAwarePaginator(
                [], // items
                0,  // total
                12, // perPage
                1,  // currentPage
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
        }
        
        return Inertia::render('PublicUmkmListSimple', [
            'umkms' => $umkms,
            'auth' => [
                'user' => Auth::user()
            ]
        ]);
    }

    public function publicUmkmShow(Umkm $umkm)
    {
        // Load UMKM with user and all menus for public viewing
        $umkm->load(['user:id,name,email', 'menus' => function($query) {
            $query->orderBy('kategori_menu')->orderBy('nama_menu');
        }]);

        return Inertia::render('PublicUmkmShow', [
            'umkm' => $umkm,
            'auth' => [
                'user' => Auth::user()
            ]
        ]);
    }
}
