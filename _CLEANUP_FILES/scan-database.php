<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     PHOTOGRAPHER SB - COMPREHENSIVE DATABASE SCHEMA SCAN       ║\n";
echo "║                    February 1, 2026                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Get all tables
$tables = DB::select('SHOW TABLES');
$dbName = env('DB_DATABASE');
$tableList = [];

foreach ($tables as $table) {
    $tableObj = (array)$table;
    $tableList[] = array_shift($tableObj);
}

echo "📊 TOTAL TABLES: " . count($tableList) . "\n";
echo "────────────────────────────────────────────────────────────────\n\n";

// Categorize tables
$systemTables = [];
$contentTables = [];
$demoDemoTables = [];

foreach ($tableList as $table) {
    // Get row count
    $count = DB::table($table)->count();
    
    // Get columns
    $columns = Schema::getColumnListing($table);
    $hasFK = false;
    foreach ($columns as $col) {
        if (strpos($col, '_id') !== false) {
            $hasFK = true;
            break;
        }
    }
    
    // Categorize
    if (in_array($table, ['migrations', 'failed_jobs', 'personal_access_tokens', 'cache_locks', 'sessions', 'jobs', 'cache', 'password_reset_tokens'])) {
        $systemTables[$table] = ['count' => $count, 'cols' => count($columns), 'hasFK' => $hasFK];
    } elseif (in_array($table, ['users', 'photographers', 'categories', 'cities', 'packages', 'photos', 'albums', 'bookings', 'reviews', 'transactions', 'verifications', 'trust_scores'])) {
        $contentTables[$table] = ['count' => $count, 'cols' => count($columns), 'hasFK' => $hasFK];
    } else {
        $demoDemoTables[$table] = ['count' => $count, 'cols' => count($columns), 'hasFK' => $hasFK];
    }
}

// Output System Tables
echo "🔧 SYSTEM TABLES (Keep Intact):\n";
foreach ($systemTables as $table => $info) {
    echo "  • $table → {$info['count']} rows | {$info['cols']} cols | FK:" . ($info['hasFK'] ? 'Yes' : 'No') . "\n";
}

echo "\n📦 CORE CONTENT TABLES (Keep & Seed):\n";
foreach ($contentTables as $table => $info) {
    echo "  • $table → {$info['count']} rows | {$info['cols']} cols | FK:" . ($info['hasFK'] ? 'Yes' : 'No') . "\n";
}

echo "\n🗑️  DEMO/TEST TABLES (Can Purge Content):\n";
foreach ($demoDemoTables as $table => $info) {
    echo "  • $table → {$info['count']} rows | {$info['cols']} cols | FK:" . ($info['hasFK'] ? 'Yes' : 'No') . "\n";
}

echo "\n\n📋 TABLE DETAILS:\n";
echo "────────────────────────────────────────────────────────────────\n\n";

foreach ($tableList as $table) {
    $count = DB::table($table)->count();
    $columns = Schema::getColumnListing($table);
    
    echo "TABLE: $table\n";
    echo "  Rows: $count\n";
    echo "  Columns: " . count($columns) . " - " . implode(', ', array_slice($columns, 0, 5)) . (count($columns) > 5 ? '...' : '') . "\n";
    
    // Check for FK relationships
    $fks = DB::select("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = '$table' AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    if (!empty($fks)) {
        echo "  Foreign Keys:\n";
        foreach ($fks as $fk) {
            echo "    - {$fk->COLUMN_NAME} → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        }
    }
    echo "\n";
}

echo "\n✅ SCAN COMPLETE\n";
