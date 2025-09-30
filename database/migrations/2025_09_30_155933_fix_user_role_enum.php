<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix the role enum to include all needed values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('umkm', 'admin', 'user') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the previous enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('umkm', 'admin') DEFAULT 'umkm'");
    }
};
