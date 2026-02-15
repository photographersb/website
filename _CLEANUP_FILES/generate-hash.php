<?php
// Generate Laravel-compatible password hash
// Run: php generate-hash.php

require __DIR__.'/vendor/autoload.php';

$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n\n";
echo "Copy this SQL and run it:\n\n";
echo "UPDATE users SET password = '{$hash}' WHERE email = 'mahidulislamnakib@gmail.com';\n";
