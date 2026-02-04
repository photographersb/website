<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Judge;
use App\Models\User;
use App\Models\Competition;
use App\Models\CompetitionJudge;
use Illuminate\Support\Facades\DB;

echo "=== COMPETITION JUDGES DIAGNOSTIC ===\n\n";

// 1. Check judges table
echo "1. JUDGES TABLE:\n";
$judgesCount = Judge::count();
echo "   Total judges: $judgesCount\n";
if ($judgesCount > 0) {
    $judges = Judge::with('user')->get();
    foreach ($judges as $judge) {
        echo "   - Judge ID: {$judge->id}, Name: {$judge->name}, User ID: {$judge->user_id}\n";
    }
}
echo "\n";

// 2. Check users with judge role
echo "2. USERS WITH ROLE='judge':\n";
$judgeUsers = User::where('role', 'judge')->get();
echo "   Count: " . $judgeUsers->count() . "\n";
foreach ($judgeUsers as $user) {
    echo "   - User ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}
echo "\n";

// 3. Check competition_judges pivot table
echo "3. COMPETITION_JUDGES PIVOT TABLE:\n";
$pivotRecords = DB::table('competition_judges')->get();
echo "   Total pivot records: " . $pivotRecords->count() . "\n";
foreach ($pivotRecords as $pivot) {
    echo "   - Competition ID: {$pivot->competition_id}, Judge ID: {$pivot->judge_id}, Judge Profile ID: " . ($pivot->judge_profile_id ?? 'NULL') . "\n";
}
echo "\n";

// 4. Check for orphaned pivot records
echo "4. ORPHANED PIVOT RECORDS:\n";
$orphaned = DB::table('competition_judges')
    ->leftJoin('users', 'competition_judges.judge_id', '=', 'users.id')
    ->whereNull('users.id')
    ->select('competition_judges.*')
    ->get();

if ($orphaned->count() > 0) {
    echo "   ❌ Found {$orphaned->count()} orphaned records (judge_id doesn't exist in users):\n";
    foreach ($orphaned as $record) {
        echo "      - Pivot ID: {$record->id}, Competition ID: {$record->competition_id}, Invalid Judge ID: {$record->judge_id}\n";
    }
} else {
    echo "   ✓ No orphaned records\n";
}
echo "\n";

// 5. Check for invalid judge_profile_id
echo "5. INVALID JUDGE_PROFILE_ID:\n";
$invalidProfiles = DB::table('competition_judges')
    ->whereNotNull('judge_profile_id')
    ->leftJoin('judges', 'competition_judges.judge_profile_id', '=', 'judges.id')
    ->whereNull('judges.id')
    ->select('competition_judges.*')
    ->get();

if ($invalidProfiles->count() > 0) {
    echo "   ❌ Found {$invalidProfiles->count()} records with invalid judge_profile_id:\n";
    foreach ($invalidProfiles as $record) {
        echo "      - Pivot ID: {$record->id}, Competition ID: {$record->competition_id}, Invalid Profile ID: {$record->judge_profile_id}\n";
    }
} else {
    echo "   ✓ No invalid judge_profile_id\n";
}
echo "\n";

// 6. Test validation simulation
echo "6. VALIDATION SIMULATION:\n";
if ($judgesCount > 0) {
    $testJudgeIds = Judge::pluck('id')->toArray();
    echo "   Valid judge IDs for validation: [" . implode(', ', $testJudgeIds) . "]\n";
    
    // Check if these exist in judges table
    $existsInJudges = Judge::whereIn('id', $testJudgeIds)->count();
    echo "   ✓ All {$existsInJudges} IDs exist in judges table\n";
} else {
    echo "   ⚠️ No judges in database to test\n";
}
echo "\n";

// 7. Check what the edit page would load
echo "7. WHAT EDIT PAGE WOULD LOAD:\n";
$competition = Competition::with(['judges', 'judgeProfiles'])->first();
if ($competition) {
    echo "   Competition: {$competition->title} (ID: {$competition->id})\n";
    echo "   judges() relationship count: " . $competition->judges->count() . "\n";
    echo "   judgeProfiles() relationship count: " . $competition->judgeProfiles->count() . "\n";
    
    if ($competition->judges->count() > 0) {
        echo "   Current judges:\n";
        foreach ($competition->judges as $cj) {
            echo "      - CompetitionJudge ID: {$cj->id}, Judge ID: {$cj->judge_id}, Profile ID: " . ($cj->judge_profile_id ?? 'NULL') . "\n";
        }
    }
}
echo "\n";

// 8. Root cause analysis
echo "8. ROOT CAUSE ANALYSIS:\n";
$issuesFound = [];

if ($judgesCount == 0) {
    $issuesFound[] = "❌ No judges in 'judges' table";
}

if ($orphaned->count() > 0) {
    $issuesFound[] = "❌ Orphaned pivot records (judge_id doesn't exist in users table)";
}

if ($invalidProfiles->count() > 0) {
    $issuesFound[] = "❌ Invalid judge_profile_id in pivot table";
}

// Check validation vs data source mismatch
echo "   Validation rule: 'judge_ids.*.exists:judges,id'\n";
echo "   Controller fetches: Judge::whereIn('id', \$judgeIds)\n";
echo "   Pivot stores: judge_id = Judge->user_id (from users table)\n\n";

if (empty($issuesFound)) {
    echo "   ✓ No immediate issues detected\n";
} else {
    echo "   Issues detected:\n";
    foreach ($issuesFound as $issue) {
        echo "   $issue\n";
    }
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
