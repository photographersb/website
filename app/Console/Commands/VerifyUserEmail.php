<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUserEmail extends Command
{
    protected $signature = 'user:verify-email {email}';
    protected $description = 'Manually verify a user email address';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        if ($user->hasVerifiedEmail()) {
            $this->info("User already verified: {$user->name} ({$email})");
            return 0;
        }

        $user->markEmailAsVerified();
        $this->info("✓ Successfully verified: {$user->name} ({$email})");
        
        return 0;
    }
}
