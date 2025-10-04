<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::all();
        $this->info('Total users: ' . $users->count());
        
        if ($users->count() > 0) {
            $this->table(['ID', 'Name', 'Email', 'Role'], $users->map(function($user) {
                return [$user->id, $user->name, $user->email, $user->role];
            })->toArray());
        } else {
            $this->warn('No users found in database');
        }
        
        return 0;
    }
}
