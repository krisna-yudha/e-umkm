<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PasswordResetRequest;
use App\Models\User;

class PasswordResetRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to create sample requests
        $users = User::where('role', 'umkm')->take(5)->get();
        
        if ($users->count() > 0) {
            // Create pending requests
            PasswordResetRequest::create([
                'user_id' => $users[0]->id,
                'reason' => 'Saya lupa password karena sudah lama tidak login ke sistem. Tolong bantu untuk reset password saya.',
                'status' => 'pending',
                'code' => null,
            ]);

            if ($users->count() > 1) {
                PasswordResetRequest::create([
                    'user_id' => $users[1]->id,
                    'reason' => 'Password saya tidak berfungsi setelah mencoba login beberapa kali. Kemungkinan akun saya bermasalah.',
                    'status' => 'pending',
                    'code' => null,
                ]);
            }

            if ($users->count() > 2) {
                // Create approved request
                $approvedRequest = PasswordResetRequest::create([
                    'user_id' => $users[2]->id,
                    'reason' => 'Lupa password setelah update browser. Mohon bantuan untuk reset password.',
                    'status' => 'approved',
                    'admin_id' => User::where('role', 'admin')->first()?->id,
                    'admin_note' => 'Permintaan disetujui. Silakan gunakan kode verifikasi untuk reset password.',
                    'approved_at' => now(),
                ]);
                $approvedRequest->generateCode();
            }

            if ($users->count() > 3) {
                // Create rejected request
                PasswordResetRequest::create([
                    'user_id' => $users[3]->id,
                    'reason' => 'Test',
                    'status' => 'rejected',
                    'admin_id' => User::where('role', 'admin')->first()?->id,
                    'admin_note' => 'Alasan terlalu singkat. Mohon berikan alasan yang lebih detail untuk permintaan reset password.',
                    'approved_at' => now(),
                ]);
            }

            if ($users->count() > 4) {
                // Create another pending request
                PasswordResetRequest::create([
                    'user_id' => $users[4]->id,
                    'reason' => 'Akun saya mungkin telah dikompromikan karena ada aktivitas mencurigakan. Mohon reset password untuk keamanan.',
                    'status' => 'pending',
                    'code' => null,
                ]);
            }
        }
    }
}