<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\UsernameService;
use Illuminate\Database\Seeder;

class GeneratePhotographerUsernames extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usernameService = app(UsernameService::class);

        // Get all photographer users without usernames
        $users = User::whereIn('role', ['photographer', 'studio_owner', 'studio_photographer'])
            ->whereNull('username')
            ->get();

        $progressBar = $this->command->getOutput()->createProgressBar(count($users));

        foreach ($users as $user) {
            // Generate username from name
            $username = $usernameService->generateUsername($user->name);
            
            // Update user with generated username
            $user->update(['username' => $username]);
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->line("\n✓ Generated usernames for " . count($users) . " photographers");
    }
}
