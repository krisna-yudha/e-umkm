<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'reason' => 'required|string|min:10|max:500',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem.']);
        }

        // Check if user already has pending request
        $existingRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->withErrors(['email' => 'Anda sudah memiliki permintaan reset password yang sedang diproses. Silakan tunggu persetujuan admin.']);
        }

        // Check if user has approved request that's still valid
        $approvedRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('created_at', '>', now()->subDays(1)) // Valid for 1 day
            ->first();

        if ($approvedRequest) {
            return redirect()->route('password.reset.verification')
                ->with('info', 'Anda sudah memiliki permintaan yang disetujui. Silakan gunakan kode verifikasi yang diberikan.')
                ->with('email', $request->email);
        }

        // Create new password reset request
        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending', // Explicitly set status
            'code' => null, // Code will be generated when admin approves
        ]);

        Log::info('Password reset request created', [
            'user_id' => $user->id,
            'email' => $user->email,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->route('password.reset.verification')
            ->with('success', 'Permintaan reset password telah dikirim ke admin. Anda akan menerima kode verifikasi setelah admin menyetujui permintaan Anda.')
            ->with('email', $request->email);
    }

    public function showVerificationForm(Request $request)
    {
        $email = $request->get('email') ?? session('email');
        
        if (!$email) {
            return redirect()->route('password.request')
                ->with('error', 'Email tidak ditemukan. Silakan ajukan permintaan reset password terlebih dahulu.');
        }

        return Inertia::render('Auth/PasswordResetVerification', [
            'email' => $email
        ]);
    }

    public function verifyCode(Request $request)
    {
        // Check if this is email verification step
        if ($request->has('action') && $request->action === 'check_email') {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem.']);
            }

            // Check if user has any approved reset request
            $resetRequest = PasswordResetRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereNotNull('code')
                ->first();

            if (!$resetRequest) {
                return back()->withErrors(['email' => 'Tidak ada permintaan reset password yang disetujui untuk email ini.']);
            }

            // Email verified, return to verification form with email
            return redirect()->route('password.reset.verification', ['email' => $request->email])
                ->with('success', 'Email terverifikasi. Silakan masukkan kode verifikasi.');
        }

        // Regular code verification
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $resetRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('status', 'approved')
            ->first();

        if (!$resetRequest) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid atau belum disetujui admin.']);
        }

        return redirect()->route('password.reset.new')
            ->with('reset_token', $resetRequest->code)
            ->with('user_id', $user->id);
    }

    public function showNewPasswordForm()
    {
        if (!session('reset_token') || !session('user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/ResetPassword', [
            'token' => session('reset_token'),
            'user_id' => session('user_id')
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find($request->user_id);
        
        if (!$user) {
            return back()->withErrors(['user_id' => 'User tidak ditemukan.']);
        }

        $resetRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('code', $request->token)
            ->where('status', 'approved')
            ->first();

        if (!$resetRequest) {
            return back()->withErrors(['token' => 'Token reset tidak valid atau sudah kadaluarsa.']);
        }

        // Update user password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete the reset request
        $resetRequest->delete();

        Log::info('Password reset completed', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}