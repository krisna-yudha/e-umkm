<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;

class PasswordResetApiController extends Controller
{
    public function checkResetStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'hasRequest' => false,
                'message' => 'Email tidak ditemukan dalam sistem'
            ], 200); // Changed from 404 to 200 for better frontend handling
        }

        $resetRequest = PasswordResetRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$resetRequest) {
            return response()->json([
                'hasRequest' => false,
                'message' => 'Belum ada permintaan reset password'
            ]);
        }

        return response()->json([
            'hasRequest' => true,
            'status' => $resetRequest->status,
            'message' => $this->getStatusMessage($resetRequest->status),
            'created_at' => $resetRequest->created_at->format('d M Y H:i'),
            'can_create_new' => in_array($resetRequest->status, ['rejected']) // Allow new request if previous was rejected
        ]);
    }

    private function getStatusMessage($status)
    {
        switch ($status) {
            case 'pending':
                return 'Permintaan Anda masih menunggu persetujuan admin';
            case 'approved':
                return 'Permintaan Anda sudah disetujui, silakan masukkan kode verifikasi';
            case 'rejected':
                return 'Permintaan sebelumnya ditolak, silakan buat permintaan baru';
            default:
                return 'Status tidak dikenal';
        }
    }
}