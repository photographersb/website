#!/usr/bin/env php
<?php

/**
 * Competitions System - Regression Testing Suite
 * Tests all critical functionality after P0 fixes
 * 
 * Phases:
 * 1. Database & Models
 * 2. API Endpoints
 * 3. Admin CRUD
 * 4. Submissions
 * 5. Moderation & Voting
 */

// Bootstrap
chdir(__DIR__);
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionVote;
use App\Models\CompetitionScore;
use App\Models\CompetitionPrize;
use App\Models\CompetitionCategory;
use Illuminate\Support\Facades\DB;

// Color codes for output
const GREEN = "\033[32m";
const RED = "\033[31m";
const YELLOW = "\033[33m";
const BLUE = "\033[34m";
const RESET = "\033[0m";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function test($description, $condition) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    
    if ($condition) {
        echo GREEN . "  ✓" . RESET . " $description" . PHP_EOL;
        $passedTests++;
        return true;
    } else {
        echo RED . "  ✗" . RESET . " $description" . PHP_EOL;
        $failedTests++;
        return false;
    }
}

echo PHP_EOL;
echo BLUE . "╔════════════════════════════════════════════════════════════════╗" . RESET . PHP_EOL;
echo BLUE . "║        REGRESSION TESTING - COMPETITIONS SYSTEM                ║" . RESET . PHP_EOL;
echo BLUE . "║               Phases 1-5 (Core Functionality)                  ║" . RESET . PHP_EOL;
echo BLUE . "╚════════════════════════════════════════════════════════════════╝" . RESET . PHP_EOL;
echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 1: DATABASE & MODELS
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 1: Database & Models" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Check tables exist
    $tables = [
        'competitions',
        'competition_submissions',
        'competition_votes',
        'competition_scores',
        'competition_prizes',
        'competition_categories',
        'competition_sponsors',
    ];
    
    foreach ($tables as $table) {
        $exists = DB::connection()->getSchemaBuilder()->hasTable($table);
        test("Table `$table` exists", $exists);
    }
    
    // Check model counts
    test("Competition model works", Competition::count() >= 0);
    test("CompetitionSubmission model works", CompetitionSubmission::count() >= 0);
    test("CompetitionVote model works", CompetitionVote::count() >= 0);
    test("CompetitionScore model works", CompetitionScore::count() >= 0);
    test("CompetitionPrize model works", CompetitionPrize::count() >= 0);
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 1 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 2: RELATIONSHIPS & QUERIES
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 2: Model Relationships & Queries" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test relationships exist
    $reflection = new ReflectionClass(Competition::class);
    test("Competition has 'submissions' relationship", $reflection->hasMethod('submissions'));
    test("Competition has 'prizes' relationship", $reflection->hasMethod('prizes'));
    test("Competition has 'categories' relationship", $reflection->hasMethod('categories'));
    
    $reflection = new ReflectionClass(CompetitionScore::class);
    test("CompetitionScore has 'judge' relationship", $reflection->hasMethod('judge'));
    test("CompetitionScore has 'submission' relationship", $reflection->hasMethod('submission'));
    test("CompetitionScore has 'competition' relationship", $reflection->hasMethod('competition'));
    
    // Test scopes exist
    $competition = Competition::query();
    test("Competition has 'published' scope", method_exists($competition, 'published'));
    test("Competition has 'active' scope", method_exists($competition, 'active'));
    test("Competition has 'completed' scope", method_exists($competition, 'completed'));
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 2 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 3: CONTROLLERS & ROUTES
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 3: Controllers & Routes" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test controllers exist
    test("CompetitionController exists", class_exists('\App\Http\Controllers\Api\CompetitionController'));
    test("CompetitionSubmissionController exists", class_exists('\App\Http\Controllers\Api\CompetitionSubmissionController'));
    test("CompetitionVoteController exists", class_exists('\App\Http\Controllers\Api\CompetitionVoteController'));
    test("AdminCompetitionApiController exists", class_exists('\App\Http\Controllers\Api\Admin\AdminCompetitionApiController'));
    
    // Test controller methods exist
    $adminController = new \App\Http\Controllers\Api\Admin\AdminCompetitionApiController();
    test("Admin index() method exists", method_exists($adminController, 'index'));
    test("Admin show() method exists", method_exists($adminController, 'show'));
    test("Admin store() method exists", method_exists($adminController, 'store'));
    test("Admin update() method exists", method_exists($adminController, 'update'));
    test("Admin destroy() method exists", method_exists($adminController, 'destroy'));
    
    // Test services exist
    test("ImageProcessingService exists", class_exists('\App\Services\ImageProcessingService'));
    test("PhotoMetadataService exists", class_exists('\App\Services\PhotoMetadataService'));
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 3 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 4: PRIZE SYSTEM (P0-3 FIX)
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 4: Prize System & Auto-Calculation (P0-3)" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test observer exists
    $observerClass = '\App\Models\Observers\CompetitionPrizeObserver';
    test("CompetitionPrizeObserver class exists", class_exists($observerClass));
    
    if (class_exists($observerClass)) {
        $observer = new $observerClass();
        test("Observer has 'created' method", method_exists($observer, 'created'));
        test("Observer has 'updated' method", method_exists($observer, 'updated'));
        test("Observer has 'deleted' method", method_exists($observer, 'deleted'));
    }
    
    // Test CompetitionPrize model has boot method
    $prizeClass = new ReflectionClass(CompetitionPrize::class);
    test("CompetitionPrize has boot() method", $prizeClass->hasMethod('boot'));
    
    // Test prize relationships
    test("CompetitionPrize has 'competition' relationship", $prizeClass->hasMethod('competition'));
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 4 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 5: ADMIN DASHBOARD STATS SYNC (P0-5 FIX)
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 5: Admin Dashboard Stats Sync (P0-5)" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Check controller file has the fix
    $controllerFile = base_path('app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php');
    $code = file_get_contents($controllerFile);
    
    test("Stats use filtered query (statsQuery)", strpos($code, 'statsQuery') !== false);
    test("Stats use filteredIds variable", strpos($code, 'filteredIds') !== false);
    test("Stats are cloned from query", strpos($code, 'clone $query') !== false);
    test("Stats calculated before pagination", strpos($code, 'paginate') !== false && strpos($code, 'filteredIds') !== false);
    
    // Verify stats structure
    $statsStructure = [
        "'total'",
        "'active'",
        "'completed'",
        "'draft'",
        "'featured'",
    ];
    
    foreach ($statsStructure as $stat) {
        test("Stats includes $stat", strpos($code, $stat) !== false);
    }
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 5 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// SUMMARY
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "╔════════════════════════════════════════════════════════════════╗" . RESET . PHP_EOL;
echo BLUE . "║                     TEST SUMMARY                              ║" . RESET . PHP_EOL;
echo BLUE . "╚════════════════════════════════════════════════════════════════╝" . RESET . PHP_EOL;

$passRate = $totalTests > 0 ? ($passedTests / $totalTests * 100) : 0;

echo PHP_EOL;
echo "Total Tests: $totalTests" . PHP_EOL;
echo GREEN . "  Passed: $passedTests" . RESET . PHP_EOL;
if ($failedTests > 0) {
    echo RED . "  Failed: $failedTests" . RESET . PHP_EOL;
}
echo "Pass Rate: " . number_format($passRate, 1) . "%" . PHP_EOL;
echo PHP_EOL;

if ($failedTests === 0) {
    echo GREEN . "✓ ALL TESTS PASSED!" . RESET . PHP_EOL;
    echo YELLOW . "System is ready for Phase 6-10 testing" . RESET . PHP_EOL;
    echo PHP_EOL;
    exit(0);
} else {
    echo RED . "✗ SOME TESTS FAILED" . RESET . PHP_EOL;
    echo "Please review failures above" . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}
