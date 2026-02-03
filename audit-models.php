<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MODEL AUDIT (Key Models) ===\n\n";

// Check critical models
$models = [
    'App\Models\Competition',
    'App\Models\Event',
    'App\Models\CompetitionSubmission',
    'App\Models\Photographer',
    'App\Models\User',
    'App\Models\Category',
    'App\Models\City',
];

foreach ($models as $modelClass) {
    if (!class_exists($modelClass)) {
        echo "✗ {$modelClass} NOT FOUND\n";
        continue;
    }
    
    try {
        $model = new $modelClass();
        echo "\n✓ " . class_basename($modelClass) . "\n";
        echo "  Table: " . $model->getTable() . "\n";
        echo "  Fillable: " . (count($model->getFillable()) > 0 ? implode(', ', array_slice($model->getFillable(), 0, 5)) . "..." : "EMPTY") . "\n";
        
        // Check for soft deletes
        if (method_exists($model, 'restore')) {
            echo "  Soft Deletes: YES\n";
        }
        
        // Check for timestamps
        echo "  Timestamps: " . ($model->usesTimestamps() ? "YES" : "NO") . "\n";
        
        // List relationships
        $reflection = new ReflectionClass($modelClass);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $relationships = [];
        
        foreach ($methods as $method) {
            $name = $method->getName();
            if (in_array($name, ['id', 'getAttribute', 'setAttribute'])) continue;
            
            try {
                $returnType = $method->getReturnType();
                if ($returnType && str_contains((string)$returnType, 'Illuminate\Database\Eloquent\Relations')) {
                    $relationships[] = $name;
                }
            } catch (\Exception $e) {}
        }
        
        if (!empty($relationships)) {
            echo "  Relations: " . implode(', ', array_slice($relationships, 0, 5)) . "\n";
        }
    } catch (\Exception $e) {
        echo "✗ {$modelClass}: " . $e->getMessage() . "\n";
    }
}

echo "\n\n=== FORM VALIDATION AUDIT ===\n\n";

// Get request classes
$requestPath = 'app/Http/Requests/';
$requestFiles = glob($requestPath . '*.php');

$criticalRequests = [
    'CompetitionStoreRequest',
    'CompetitionUpdateRequest',
    'EventStoreRequest',
    'EventUpdateRequest',
];

foreach ($criticalRequests as $requestName) {
    $file = $requestPath . $requestName . '.php';
    if (!file_exists($file)) {
        echo "✗ {$requestName} NOT FOUND\n";
        continue;
    }
    
    echo "\n✓ {$requestName}\n";
    
    // Read the file and extract rules
    $content = file_get_contents($file);
    
    // Find the rules method
    if (preg_match('/public function rules.*?\{(.*?)\n\s*\}/s', $content, $matches)) {
        $rulesContent = $matches[1];
        
        // Count validation rules
        preg_match_all('/[\'"]([a-z_\.]*)[\'"].*?=>', $rulesContent, $fieldMatches);
        $fields = array_unique($fieldMatches[1]);
        
        echo "  Validated Fields: " . count($fields) . "\n";
        echo "  Sample: " . implode(', ', array_slice($fields, 0, 5)) . "\n";
    }
}

echo "\n\n=== CONTROLLER SCAN ===\n\n";

// Check key controllers exist
$controllers = [
    'app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php',
    'app/Http/Controllers/Api/Admin/AdminEventApiController.php',
    'app/Http/Controllers/Api/CityController.php',
    'app/Http/Controllers/Api/AdminController.php',
];

foreach ($controllers as $file) {
    if (file_exists($file)) {
        echo "✓ " . basename($file) . "\n";
        
        // Count lines
        $lines = count(file($file));
        echo "  Lines: $lines\n";
    } else {
        echo "✗ " . basename($file) . " NOT FOUND\n";
    }
}

echo "\n\n=== DATABASE RELATIONSHIPS CHECK ===\n\n";

// Check key foreign keys
$fkChecks = [
    'competitions.admin_id → users.id',
    'competitions.organizer_id → photographers.id',
    'competitions.category_id → categories.id',
    'events.organizer_id → photographers.id',
    'events.city_id → cities.id',
    'competition_submissions.photographer_id → photographers.id',
    'competition_judges.judge_id → users.id',
];

$keyConstraints = DB::select("
    SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL
    ORDER BY TABLE_NAME, COLUMN_NAME
");

$fkMap = [];
foreach ($keyConstraints as $fk) {
    $key = "{$fk->TABLE_NAME}.{$fk->COLUMN_NAME} → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}";
    $fkMap[] = $key;
}

foreach ($fkChecks as $check) {
    if (in_array($check, $fkMap)) {
        echo "✓ {$check}\n";
    } else {
        echo "✗ {$check} - MISSING FK\n";
    }
}

echo "\n=== CRITICAL FINDINGS ===\n\n";

// Check if key columns exist
$criticalColumns = [
    ['users', 'username'],
    ['photographers', 'slug'],
    ['competitions', 'status'],
    ['competitions', 'published_at'],
    ['competitions', 'submission_deadline'],
    ['events', 'city_id'],
    ['events', 'organizer_id'],
    ['competition_submissions', 'status'],
    ['competition_submissions', 'category_id'],
];

foreach ($criticalColumns as [$table, $column]) {
    $exists = DB::selectOne("
        SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ", [$table, $column]);
    
    $status = $exists ? "✓" : "✗";
    echo "{$status} {$table}.{$column}\n";
}

echo "\n";
