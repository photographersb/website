<?php
require __DIR__.'/vendor/autoload.php';

$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n";
echo "Verify: " . (password_verify($password, $hash) ? 'VALID ✓' : 'INVALID ✗') . "\n\n";

echo "SQL Update:\n";
echo "UPDATE users SET password = '{$hash}' WHERE email = 'mahidulislamnakib@gmail.com';\n";
