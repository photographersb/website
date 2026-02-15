<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = App\Models\User::orderBy('id', 'desc')->limit(10)->get(['id', 'name', 'email', 'email_verified_at', 'approval_status']);

echo "Recent Users - Approval Status:\n";
echo str_repeat('-', 100) . "\n";
foreach($users as $user) {
    $verified = $user->email_verified_at ? 'VERIFIED' : 'NOT VERIFIED';
    $approval = strtoupper($user->approval_status ?? 'NULL');
    printf("ID: %d | %-25s | %-35s | %-12s | %s\n", 
        $user->id, 
        substr($user->name, 0, 25), 
        substr($user->email, 0, 35), 
        $verified,
        $approval
    );
}
echo str_repeat('-', 100) . "\n";

// Approve all pending users
$pendingUsers = App\Models\User::where('approval_status', 'pending')->get();
if ($pendingUsers->count() > 0) {
    echo "\nApproving pending users...\n";
    foreach ($pendingUsers as $user) {
        $user->update(['approval_status' => 'approved']);
        echo "✓ Approved: {$user->name} ({$user->email})\n";
    }
} else {
    echo "\nNo pending users to approve.\n";
}
