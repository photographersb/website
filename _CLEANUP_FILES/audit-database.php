<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DATABASE SCHEMA AUDIT ===\n\n";

// Get all tables
$tables = DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME');

echo "TABLES IN DATABASE (" . count($tables) . " total):\n";
echo str_repeat("-", 80) . "\n";

$tableNames = [];
foreach ($tables as $table) {
    $tableName = $table->TABLE_NAME;
    $tableNames[] = $tableName;
    
    // Get column count
    $columns = Schema::getColumns($tableName);
    $colCount = count($columns);
    
    echo "  {$tableName} ({$colCount} columns)\n";
}

echo "\n";

// Get migration files
echo "MIGRATION FILES:\n";
echo str_repeat("-", 80) . "\n";

$migrationPath = 'database/migrations/';
$migrationFiles = glob($migrationPath . '*.php');
$migrations = [];

foreach ($migrationFiles as $file) {
    $name = basename($file);
    $migrations[] = $name;
    echo "  {$name}\n";
}

echo "\n";

// Compare tables to known models
echo "MODELS vs TABLES:\n";
echo str_repeat("-", 80) . "\n";

$modelsPath = 'app/Models/';
$modelFiles = glob($modelsPath . '*.php');
$models = [];

foreach ($modelFiles as $file) {
    $name = basename($file, '.php');
    if ($name === 'traits') continue;
    
    $models[] = $name;
}

// Check each model for table existence
foreach (array_slice($models, 0, 20) as $model) {
    $modelClass = "App\\Models\\{$model}";
    
    if (!class_exists($modelClass)) {
        continue;
    }
    
    try {
        $instance = new $modelClass();
        $tableName = $instance->getTable();
        $exists = in_array($tableName, $tableNames);
        
        $status = $exists ? "✓" : "✗";
        echo "  {$status} {$model} → table: {$tableName}\n";
        
        if (!$exists) {
            echo "      !! TABLE NOT FOUND\n";
        }
    } catch (\Exception $e) {
        echo "  ? {$model} (error loading)\n";
    }
}

echo "\n=== DETAILED COLUMN INSPECTION ===\n";
echo str_repeat("-", 80) . "\n";

// Inspect critical tables
$criticalTables = [
    'users', 'photographers', 'competitions', 'events', 'competition_submissions',
    'competition_prizes', 'competition_judges', 'competition_sponsors', 'cities',
    'categories', 'event_rsvps'
];

foreach ($criticalTables as $table) {
    if (!in_array($table, $tableNames)) {
        echo "\n✗ TABLE NOT FOUND: {$table}\n";
        continue;
    }
    
    $columns = Schema::getColumns($table);
    echo "\n✓ {$table} (" . count($columns) . " columns):\n";
    
    foreach ($columns as $col) {
        $name = $col['name'];
        $type = $col['type'];
        $nullable = $col['nullable'] ? 'NULL' : 'NOT NULL';
        echo "    - {$name} ({$type}) {$nullable}\n";
    }
}

echo "\n";
