<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user with the email from the form
        User::firstOrCreate(
            ['email' => 'gentungkuy@gmail.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'role' => 'umkm',
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('Test user created: gentungkuy@gmail.com');
    }
}