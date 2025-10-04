<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Hash;

class AdminPasswordResetTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create test users
        $user1 = User::firstOrCreate([
            'email' => 'user1@test.com'
        ], [
            'name' => 'Test User 1',
            'password' => Hash::make('password'),
            'role' => 'umkm'
        ]);

        $user2 = User::firstOrCreate([
            'email' => 'user2@test.com'
        ], [
            'name' => 'Test User 2',
            'password' => Hash::make('password'),
            'role' => 'umkm'
        ]);

        $user3 = User::firstOrCreate([
            'email' => 'user3@test.com'
        ], [
            'name' => 'Test User 3',
            'password' => Hash::make('password'),
            'role' => 'umkm'
        ]);

        // Clear existing requests
        PasswordResetRequest::truncate();

        // Create pending request
        PasswordResetRequest::create([
            'user_id' => $user1->id,
            'reason' => 'Saya lupa password karena sudah lama tidak login. Mohon bantuan untuk reset password saya agar bisa mengakses sistem kembali.',
            'status' => 'pending',
            'created_at' => now()->subHours(2),
        ]);

        // Create approved request
        $approvedRequest = PasswordResetRequest::create([
            'user_id' => $user2->id,
            'reason' => 'Password tidak berfungsi setelah update browser. Sudah mencoba beberapa kali tapi gagal terus.',
            'status' => 'approved',
            'admin_id' => $admin->id,
            'admin_note' => 'Permintaan valid. Kode verifikasi telah dibuat.',
            'approved_at' => now()->subHour(),
            'created_at' => now()->subHours(4),
        ]);
        $approvedRequest->generateCode();

        // Create rejected request
        PasswordResetRequest::create([
            'user_id' => $user3->id,
            'reason' => 'Lupa password',
            'status' => 'rejected',
            'admin_id' => $admin->id,
            'admin_note' => 'Alasan terlalu singkat. Mohon berikan penjelasan yang lebih detail.',
            'created_at' => now()->subDay(),
        ]);

        echo "Test data created successfully!\n";
        echo "Admin: admin@test.com / password\n";
        echo "Users: user1@test.com, user2@test.com, user3@test.com / password\n";
    }
}