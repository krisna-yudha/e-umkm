<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Check admin authorization
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $totalUmkm = Umkm::count();
            $activeUmkm = Umkm::where('status', 'active')->count();
            $inactiveUmkm = Umkm::where('status', 'inactive')->count();
            $totalUsers = User::where('role', 'umkm')->count();

            $recentUmkm = Umkm::with('user')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'total_umkm' => $totalUmkm,
                    'active_umkm' => $activeUmkm,
                    'inactive_umkm' => $inactiveUmkm,
                    'total_users' => $totalUsers,
                ],
                'recent_umkm' => $recentUmkm
            ]);
        } catch (\Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard');
        }
    }

    public function umkmIndex()
    {
        // Check admin authorization
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $umkms = Umkm::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return Inertia::render('Admin/UmkmManagement', [
                'umkms' => $umkms
            ]);
        } catch (\Exception $e) {
            Log::error('Admin UMKM index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data UMKM');
        }
    }

    public function toggleUmkmStatus(Request $request, $id)
    {
        // Check admin authorization
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $umkm = Umkm::findOrFail($id);
            $newStatus = $umkm->status === 'active' ? 'inactive' : 'active';
            
            $umkm->update(['status' => $newStatus]);

            Log::info('Admin toggled UMKM status', [
                'admin_id' => Auth::id(),
                'umkm_id' => $id,
                'old_status' => $umkm->status,
                'new_status' => $newStatus
            ]);

            return redirect()->back()->with('success', 'Status UMKM berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Admin toggle UMKM status error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status UMKM');
        }
    }

    public function reports()
    {
        // Check admin authorization
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            // Monthly UMKM registration statistics
            $monthlyStats = Umkm::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get();

            // Category statistics
            $categoryStats = Umkm::selectRaw('kategori, COUNT(*) as count')
                ->groupBy('kategori')
                ->get();

            // Status statistics
            $statusStats = Umkm::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            return Inertia::render('Admin/Reports', [
                'monthly_stats' => $monthlyStats,
                'category_stats' => $categoryStats,
                'status_stats' => $statusStats
            ]);
        } catch (\Exception $e) {
            Log::error('Admin reports error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat laporan');
        }
    }
}
