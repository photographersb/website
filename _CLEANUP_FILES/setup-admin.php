<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'mahidulislamnakib@gmail.com')->first();

if ($user) {
    // Reset password to password123
    $user->password = Hash::make('password123');
    $user->save();
    
    // Delete old tokens
    $user->tokens()->delete();
    
    // Generate new token
    $token = $user->createToken('auto-login')->plainTextToken;
    
    echo "✓ Admin user configured successfully!\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Password reset to: password123\n";
    echo "New token: " . substr($token, 0, 20) . "...\n";
    echo "\nTest password verification:\n";
    echo "Password check: " . (Hash::check('password123', $user->password) ? 'VALID' : 'INVALID') . "\n";
} else {
    echo "ERROR: User not found!\n";
}
?>
