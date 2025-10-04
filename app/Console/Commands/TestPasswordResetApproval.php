<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PasswordResetRequest;
use App\Models\User;

class TestPasswordResetApproval extends Command
{
    protected $signature = 'test:password-reset-approval';
    protected $description = 'Test password reset approval process';

    public function handle()
    {
        $this->info('Testing password reset approval process...');

        // Find a pending request
        $pendingRequest = PasswordResetRequest::where('status', 'pending')->first();
        
        if (!$pendingRequest) {
            $this->error('No pending password reset requests found.');
            return 1;
        }

        $this->info("Found pending request ID: {$pendingRequest->id}");
        $this->info("User: {$pendingRequest->user->name} ({$pendingRequest->user->email})");
        $this->info("Current status: {$pendingRequest->status}");
        $this->info("Current code: " . ($pendingRequest->code ?? 'NULL'));

        // Test approval
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('No admin user found.');
            return 1;
        }

        $this->info("Approving with admin: {$admin->name}");
        
        try {
            $pendingRequest->approve($admin->id, 'Test approval via command');
            
            // Refresh the model
            $pendingRequest->refresh();
            
            $this->info("After approval:");
            $this->info("Status: {$pendingRequest->status}");
            $this->info("Code: {$pendingRequest->code}");
            $this->info("Admin ID: {$pendingRequest->admin_id}");
            $this->info("Approved at: {$pendingRequest->approved_at}");
            
            $this->info('Password reset approval test completed successfully!');
            
        } catch (\Exception $e) {
            $this->error("Error during approval: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}