<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;

echo "=== User Type Check ===\n";
$users = User::with('umkms')->get();

foreach ($users as $user) {
    $umkmCount = $user->umkms->count();
    echo sprintf("%-30s | user_type: %-6s | role: %-8s | UMKMs: %d\n", 
        $user->email, 
        $user->user_type, 
        $user->role,
        $umkmCount
    );
}
