#!/usr/bin/env php
<?php
/**
 * Fresh Database Setup Script
 * Drops all data and reseeds with production-ready essentials
 * Keeps super admin: mahidulislamnakib@gmail.com
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════╗\n";
echo "║  🔄 FRESH DATABASE SETUP                               ║\n";
echo "║  ⚠️  WARNING: This will DROP ALL DATA!                 ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n";
echo "\n";

// Confirm action
echo "This script will:\n";
echo "  1. Drop all tables\n";
echo "  2. Run fresh migrations\n";
echo "  3. Seed essential production data\n";
echo "  4. Keep super admin: mahidulislamnakib@gmail.com\n";
echo "\n";
echo "Are you sure? Type 'yes' to continue: ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if(trim($line) != 'yes'){
    echo "\n❌ Operation cancelled.\n\n";
    exit;
}
fclose($handle);

echo "\n🔄 Starting fresh database setup...\n\n";

// Execute migrate:fresh with seeder
passthru('php artisan migrate:fresh --seed');

echo "\n";
echo "╔═══════════════════════════════════════════════════════╗\n";
echo "║  ✅ DATABASE RESET COMPLETE                            ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n";
echo "\n";
echo "🔐 Super Admin Credentials:\n";
echo "  Email: mahidulislamnakib@gmail.com\n";
echo "  Password: SuperAdmin@2026\n";
echo "  ⚠️  Change password after first login!\n";
echo "\n";
echo "🌐 Access the application:\n";
echo "  Admin Panel: http://127.0.0.1:8000/admin/login\n";
echo "  Website: http://127.0.0.1:8000\n";
echo "\n";
