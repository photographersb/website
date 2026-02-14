<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "Checking user ID 2...\n";
$user = User::find(2);
if ($user) {
    echo "User found: {$user->name}, Role: {$user->role}\n";
    
    if ($user->role !== 'judge') {
        echo "User is not a judge. Deleting invalid pivot record...\n";
        DB::table('competition_judges')->where('id', 3)->delete();
        echo "✓ Deleted pivot record ID 3\n";
    }
} else {
    echo "User ID 2 not found. Deleting invalid pivot record...\n";
    DB::table('competition_judges')->where('id', 3)->delete();
    echo "✓ Deleted pivot record ID 3\n";
}

echo "\nFinal verification:\n";
$remaining = DB::table('competition_judges')->whereNull('judge_profile_id')->count();
echo "Records with NULL judge_profile_id: $remaining\n";

if ($remaining == 0) {
    echo "✓ All pivot records now have valid judge_profile_id\n";
}
