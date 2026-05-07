<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set user_type='umkm' for all users who have UMKM businesses
        DB::table('users')
            ->whereIn('id', DB::table('umkms')->select('user_id')->distinct())
            ->update(['user_type' => 'umkm']);

        // Ensure admin users keep user_type='user'
        DB::table('users')
            ->where('role', 'admin')
            ->update(['user_type' => 'user']);

        // Ensure all other users have user_type='user'
        DB::table('users')
            ->whereNotIn('role', ['admin', 'umkm'])
            ->orWhereNull('user_type')
            ->update(['user_type' => 'user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all user_type back to 'user' if need to rollback
        DB::table('users')->update(['user_type' => 'user']);
    }
};

