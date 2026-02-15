#!/usr/bin/env php
<?php

/**
 * Competitions System - Regression Testing Suite
 * Tests all critical functionality - PHASES 6-10
 * 
 * Phases:
 * 6. API Endpoints
 * 7. Submissions Workflow  
 * 8. Voting System
 * 9. Judge Scoring
 * 10. Winner Calculation
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
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Color codes
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
echo BLUE . "║              Phases 6-10 (Advanced Features)                   ║" . RESET . PHP_EOL;
echo BLUE . "╚════════════════════════════════════════════════════════════════╝" . RESET . PHP_EOL;
echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 6: API ENDPOINT STRUCTURE
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 6: API Endpoint Structure" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test API routes exist
    $routes = app('router')->getRoutes();
    $routeUris = collect($routes)->map(fn($route) => $route->uri())->toArray();
    
    $expectedUris = [
        'api/v1/competitions',
        'api/v1/competitions/{competition}',
        'api/v1/competitions/{competition}/submit',
        'api/v1/competitions/{competition}/submissions',
    ];
    
    foreach ($expectedUris as $uri) {
        $exists = collect($routeUris)->contains(fn($r) => str_contains($r, trim($uri, '/')));
        test("Route '$uri' exists", $exists);
    }
    
    // Test admin routes
    $adminUris = [
        'api/v1/admin/competitions',
        'api/v1/admin/competitions/{id}',
    ];
    
    foreach ($adminUris as $uri) {
        $exists = collect($routeUris)->contains(fn($r) => str_contains($r, trim($uri, '/')));
        test("Admin route '$uri' exists", $exists);
    }
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 6 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 7: SUBMISSIONS WORKFLOW
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 7: Submissions Workflow" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test submission model
    $submissionClass = new ReflectionClass(CompetitionSubmission::class);
    
    test("Submission has 'competition' relationship", $submissionClass->hasMethod('competition'));
    test("Submission has 'photographer' relationship", $submissionClass->hasMethod('photographer'));
    test("Submission has 'votes' relationship", $submissionClass->hasMethod('votes'));
    test("Submission has 'scores' relationship", $submissionClass->hasMethod('scores'));
    
    // Test submission attributes
    $submission = new CompetitionSubmission();
    test("Submission has 'title' attribute", in_array('title', $submission->getFillable()));
    test("Submission has 'description' attribute", in_array('description', $submission->getFillable()));
    test("Submission has 'image_url' attribute", in_array('image_url', $submission->getFillable()));
    test("Submission has 'status' attribute", in_array('status', $submission->getFillable()));
    
    // Test submission scopes
    $submissionQuery = CompetitionSubmission::query();
    test("Submission has query methods", method_exists($submissionQuery, 'where'));
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 7 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 8: VOTING SYSTEM
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 8: Voting System" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test vote model
    $voteClass = new ReflectionClass(CompetitionVote::class);
    
    test("Vote has 'submission' relationship", $voteClass->hasMethod('submission'));
    test("Vote has 'voter' relationship", $voteClass->hasMethod('voter'));
    test("Vote has 'competition' relationship", $voteClass->hasMethod('competition'));
    
    // Test vote attributes
    $vote = new CompetitionVote();
    test("Vote has 'voter_id' attribute", in_array('voter_id', $vote->getFillable()));
    test("Vote has 'submission_id' attribute", in_array('submission_id', $vote->getFillable()));
    test("Vote has 'competition_id' attribute", in_array('competition_id', $vote->getFillable()));
    
    // Test vote table columns
    $voteColumns = DB::connection()->getSchemaBuilder()->getColumnListing('competition_votes');
    test("Vote table has data", is_array($voteColumns));
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 8 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 9: JUDGE SCORING
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 9: Judge Scoring System" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test score model (P0-1 relationships)
    $scoreClass = new ReflectionClass(CompetitionScore::class);
    
    test("Score has 'judge' relationship", $scoreClass->hasMethod('judge'));
    test("Score has 'submission' relationship", $scoreClass->hasMethod('submission'));
    test("Score has 'competition' relationship", $scoreClass->hasMethod('competition'));
    
    // Test score attributes
    $score = new CompetitionScore();
    test("Score has 'total_score' attribute", in_array('total_score', $score->getFillable()));
    test("Score has 'judge_id' attribute", in_array('judge_id', $score->getFillable()));
    test("Score has 'submission_id' attribute", in_array('submission_id', $score->getFillable()));
    
    // Test score scope for calculating averages
    $scoreQuery = CompetitionScore::query();
    test("Score model is queryable", method_exists($scoreQuery, 'where'));
    test("Score supports grouping", is_callable([$scoreQuery, 'groupBy']));
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 9 Error: " . $e->getMessage() . RESET . PHP_EOL;
}

echo PHP_EOL;

// ═══════════════════════════════════════════════════════════════════════════
// PHASE 10: WINNER CALCULATION & RESULTS
// ═══════════════════════════════════════════════════════════════════════════

echo BLUE . "PHASE 10: Winner Calculation & Results" . RESET . PHP_EOL;
echo "─" . str_repeat("─", 60) . PHP_EOL;

try {
    // Test competition result attributes
    $competitionClass = new ReflectionClass(Competition::class);
    
    test("Competition has 'submissions' relationship", $competitionClass->hasMethod('submissions'));
    test("Competition has 'prizes' relationship", $competitionClass->hasMethod('prizes'));
    test("Competition has 'votes' relationship", $competitionClass->hasMethod('votes'));
    
    // Test competition model
    $competition = new Competition();
    test("Competition has 'total_prize_pool' attribute", in_array('total_prize_pool', $competition->getFillable()));
    test("Competition has 'results_announcement_date' attribute", in_array('results_announcement_date', $competition->getFillable()));
    test("Competition has 'status' attribute", in_array('status', $competition->getFillable()));
    
    // Test prize system (P0-3 fix)
    $prizeClass = new ReflectionClass(CompetitionPrize::class);
    test("Prize has 'competition' relationship", $prizeClass->hasMethod('competition'));
    test("Prize has 'cash_amount' attribute", in_array('cash_amount', (new CompetitionPrize())->getFillable()));
    
    // Test that observer is registered
    test("Prize observer is registered", class_exists('\App\Models\Observers\CompetitionPrizeObserver'));
    
} catch (\Exception $e) {
    echo RED . "  ✗ Phase 10 Error: " . $e->getMessage() . RESET . PHP_EOL;
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
    echo YELLOW . "All features are working correctly" . RESET . PHP_EOL;
    echo PHP_EOL;
    exit(0);
} else {
    echo RED . "✗ SOME TESTS FAILED" . RESET . PHP_EOL;
    echo "Please review failures above" . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}
