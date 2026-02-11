<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = App\Models\User::orderBy('id', 'desc')->limit(10)->get(['id', 'name', 'email', 'email_verified_at']);

echo "Recent Users:\n";
echo str_repeat('-', 80) . "\n";
foreach($users as $user) {
    $verified = $user->email_verified_at ? date('Y-m-d H:i', strtotime($user->email_verified_at)) : 'NOT VERIFIED';
    printf("ID: %d | %s | %s | %s\n", $user->id, $user->name, $user->email, $verified);
}
echo str_repeat('-', 80) . "\n";
echo "Total users: " . App\Models\User::count() . "\n";
