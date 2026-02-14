<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Judge;
use App\Models\User;
use App\Models\Competition;
use App\Models\CompetitionJudge;
use Illuminate\Support\Facades\DB;

echo "=== FIXING COMPETITION JUDGES PIVOT DATA ===\n\n";

// 1. Check current state
echo "1. CURRENT STATE:\n";
$totalPivot = DB::table('competition_judges')->count();
echo "   Total pivot records: $totalPivot\n";

$nullProfileIds = DB::table('competition_judges')
    ->whereNull('judge_profile_id')
    ->count();
echo "   Records with NULL judge_profile_id: $nullProfileIds\n\n";

if ($nullProfileIds == 0) {
    echo "   ✓ All records already have judge_profile_id\n\n";
} else {
    echo "2. FIXING NULL JUDGE_PROFILE_ID:\n";
    
    // Get all pivot records with NULL judge_profile_id
    $recordsToFix = DB::table('competition_judges')
        ->whereNull('judge_profile_id')
        ->get();
    
    foreach ($recordsToFix as $record) {
        // judge_id in pivot is actually the user_id
        // We need to find the judge profile for this user_id
        $judge = Judge::where('user_id', $record->judge_id)->first();
        
        if ($judge) {
            DB::table('competition_judges')
                ->where('id', $record->id)
                ->update(['judge_profile_id' => $judge->id]);
            
            echo "   ✓ Fixed record ID {$record->id}: Competition {$record->competition_id}, set judge_profile_id = {$judge->id} (for user {$record->judge_id})\n";
        } else {
            echo "   ⚠️  Record ID {$record->id}: No judge profile found for user_id {$record->judge_id}\n";
            
            // Check if this user exists in users table with role=judge
            $user = User::where('id', $record->judge_id)->where('role', 'judge')->first();
            
            if ($user) {
                echo "      → User exists but has no judge profile. Creating one...\n";
                
                // Create a judge profile for this user
                $newJudge = Judge::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'bio' => 'Professional photography judge',
                    'is_active' => true,
                    'sort_order' => 999,
                ]);
                
                // Update pivot record
                DB::table('competition_judges')
                    ->where('id', $record->id)
                    ->update(['judge_profile_id' => $newJudge->id]);
                
                echo "      ✓ Created judge profile ID {$newJudge->id} and updated pivot record\n";
            } else {
                echo "      ⚠️  User {$record->judge_id} not found or not a judge. Consider deleting this pivot record.\n";
            }
        }
    }
}

echo "\n3. CHECKING FOR ORPHANED RECORDS:\n";

// Check for records where judge_id doesn't exist in users
$orphanedByUserId = DB::table('competition_judges')
    ->leftJoin('users', 'competition_judges.judge_id', '=', 'users.id')
    ->whereNull('users.id')
    ->select('competition_judges.*')
    ->get();

if ($orphanedByUserId->count() > 0) {
    echo "   ⚠️  Found {$orphanedByUserId->count()} records where judge_id doesn't exist in users table:\n";
    foreach ($orphanedByUserId as $record) {
        echo "      - Pivot ID: {$record->id}, Competition: {$record->competition_id}, Invalid judge_id: {$record->judge_id}\n";
    }
    
    echo "\n   Delete these orphaned records? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) == 'y') {
        foreach ($orphanedByUserId as $record) {
            DB::table('competition_judges')->where('id', $record->id)->delete();
            echo "      ✓ Deleted pivot record ID {$record->id}\n";
        }
    }
    fclose($handle);
} else {
    echo "   ✓ No orphaned records (all judge_ids exist in users table)\n";
}

// Check for records where judge_profile_id doesn't exist in judges
$orphanedByProfileId = DB::table('competition_judges')
    ->whereNotNull('judge_profile_id')
    ->leftJoin('judges', 'competition_judges.judge_profile_id', '=', 'judges.id')
    ->whereNull('judges.id')
    ->select('competition_judges.*')
    ->get();

if ($orphanedByProfileId->count() > 0) {
    echo "\n   ⚠️  Found {$orphanedByProfileId->count()} records where judge_profile_id doesn't exist in judges table:\n";
    foreach ($orphanedByProfileId as $record) {
        echo "      - Pivot ID: {$record->id}, Competition: {$record->competition_id}, Invalid profile_id: {$record->judge_profile_id}\n";
    }
    
    echo "\n   Set judge_profile_id to NULL for these records? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) == 'y') {
        foreach ($orphanedByProfileId as $record) {
            DB::table('competition_judges')->where('id', $record->id)->update(['judge_profile_id' => null]);
            echo "      ✓ Set judge_profile_id to NULL for pivot record ID {$record->id}\n";
        }
    }
    fclose($handle);
} else {
    echo "   ✓ No invalid judge_profile_id records\n";
}

echo "\n4. FINAL STATE:\n";
$totalAfter = DB::table('competition_judges')->count();
$withProfiles = DB::table('competition_judges')->whereNotNull('judge_profile_id')->count();
$withoutProfiles = DB::table('competition_judges')->whereNull('judge_profile_id')->count();

echo "   Total pivot records: $totalAfter\n";
echo "   With judge_profile_id: $withProfiles\n";
echo "   Without judge_profile_id: $withoutProfiles\n";

if ($withoutProfiles > 0) {
    echo "   ⚠️  Still have records without judge_profile_id. These may cause validation errors.\n";
} else {
    echo "   ✓ All records now have valid judge_profile_id\n";
}

echo "\n5. VERIFICATION:\n";
$competitions = Competition::with('judges')->get();
foreach ($competitions as $comp) {
    if ($comp->judges->count() > 0) {
        echo "   Competition: {$comp->title}\n";
        foreach ($comp->judges as $cj) {
            echo "      - Judge ID: {$cj->judge_id}, Profile ID: " . ($cj->judge_profile_id ?? 'NULL') . "\n";
        }
    }
}

echo "\n=== FIX COMPLETE ===\n";
echo "\nNext steps:\n";
echo "1. Run: npm run build\n";
echo "2. Run: php artisan optimize:clear\n";
echo "3. Test competition edit page in browser\n";
