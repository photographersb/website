<?php
/**
 * Auto Login - Just visit this page to login automatically
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

// Clear rate limiter
Cache::flush();

// Get or create super admin
$user = User::where('email', 'mahidulislamnakib@gmail.com')->first();

if (!$user) {
    die("ERROR: User not found!");
}

// Reset password
$user->password = Hash::make('password123');
$user->save();

// Generate token
$user->tokens()->delete();
$token = $user->createToken('auto-login')->plainTextToken;

$userData = [
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'role' => $user->role
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Auto Login - Photographer SB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .container {
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .success {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">✓</div>
        <h1>Logging you in...</h1>
        <div class="spinner"></div>
        <p>Password reset to: <strong>password123</strong></p>
        <p id="status">Setting up authentication...</p>
    </div>

    <script>
        // Store authentication data in the exact format expected by the SPA
        localStorage.setItem('auth_token', '<?php echo $token; ?>');
        localStorage.setItem('user', '<?php echo json_encode($userData); ?>');
        localStorage.setItem('user_role', '<?php echo $user->role; ?>');
        
        document.getElementById('status').textContent = 'Redirecting to admin dashboard...';
        
        // Redirect after 1 second
        setTimeout(function() {
            window.location.href = '/admin/dashboard';
        }, 1000);
    </script>
</body>
</html>
