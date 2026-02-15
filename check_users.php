<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$emails = ['kutub@mail.com', 'rahim@mail.com'];

echo "==========================================\n";
echo "Checking Users\n";
echo "==========================================\n\n";

foreach ($emails as $email) {
    $user = User::where('email', $email)->first();
    
    if ($user) {
        echo "✅ {$email}\n";
        echo "   ID: {$user->id}\n";
        echo "   Name: {$user->name}\n";
        echo "   Role: {$user->role}\n";
        echo "   Status: {$user->status}\n";
        echo "   Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
        
        // Check event registrations
        $registrations = \App\Models\EventRegistration::where('user_id', $user->id)
            ->where('event_id', 1)
            ->count();
        echo "   Event Registrations: {$registrations}\n\n";
    } else {
        echo "❌ {$email} - NOT FOUND\n\n";
    }
}
