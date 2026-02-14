<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'mahidulislamnakib@gmail.com')->first();

if ($user) {
    $user->approval_status = 'approved';
    $user->approved_at = now();
    $user->save();
    
    echo "✅ Account approved successfully!\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Status: {$user->approval_status}\n";
} else {
    echo "❌ User not found\n";
}
