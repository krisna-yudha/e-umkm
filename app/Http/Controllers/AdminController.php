<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Umkm;
use App\Models\User;
use App\Models\PasswordResetRequest;
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

    public function passwordResetRequests()
    {
        // Check admin authorization
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $requests = PasswordResetRequest::with(['user', 'admin'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return Inertia::render('Admin/PasswordResetRequests', [
                'requests' => $requests
            ]);
        } catch (\Exception $e) {
            Log::error('Admin password reset requests error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat permintaan reset password');
        }
    }

    public function approvePasswordReset(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        // Check admin authorization
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'note' => 'nullable|string|max:500'
        ]);

        try {
            // Check if request is still pending
            if ($passwordResetRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
            }

            // Generate code first
            $verificationCode = $passwordResetRequest->generateCode();
            
            // Then approve with admin info
            $passwordResetRequest->approve(Auth::id(), $request->note);

            Log::info('Password reset request approved', [
                'request_id' => $passwordResetRequest->id,
                'user_id' => $passwordResetRequest->user_id,
                'admin_id' => Auth::id(),
                'verification_code' => $verificationCode
            ]);

            return redirect()->back()->with('success', 'Permintaan reset password telah disetujui. Kode verifikasi: ' . $verificationCode);
        } catch (\Exception $e) {
            Log::error('Password reset approval error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui permintaan: ' . $e->getMessage());
        }
    }

    public function rejectPasswordReset(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        // Check admin authorization
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'note' => 'required|string|min:5|max:500'
        ]);

        try {
            // Check if request is still pending
            if ($passwordResetRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
            }

            $passwordResetRequest->reject(Auth::id(), $request->note);

            Log::info('Password reset request rejected', [
                'request_id' => $passwordResetRequest->id,
                'user_id' => $passwordResetRequest->user_id,
                'admin_id' => Auth::id(),
                'note' => $request->note
            ]);

            return redirect()->back()->with('success', 'Permintaan reset password telah ditolak.');
        } catch (\Exception $e) {
            Log::error('Password reset rejection error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak permintaan: ' . $e->getMessage());
        }
    }
}
