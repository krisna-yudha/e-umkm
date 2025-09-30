<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@admin.com')->first();
        
        if (!$existingAdmin) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'role' => 'admin',
                'password' => Hash::make('admin'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
