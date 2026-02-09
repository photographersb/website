<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'mahidulislamnakib@gmail.com')->first();

if ($user) {
    echo "Super Admin Check:\n";
    echo "==================\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "approval_status: " . ($user->approval_status ?? 'NULL') . "\n";
    echo "approved_at: " . ($user->approved_at ?? 'NULL') . "\n";
    echo "email_verified_at: " . ($user->email_verified_at ?? 'NULL') . "\n";
} else {
    echo "Super admin not found!\n";
}
