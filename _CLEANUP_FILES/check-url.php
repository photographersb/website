<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('username', 'nasimnewaz')->first();
if ($user) {
    $service = app(\App\Services\UsernameService::class);
    echo "User ID: " . $user->id . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Profile URL: " . $service->getProfileUrl($user) . "\n";
} else {
    echo "User not found\n";
}
