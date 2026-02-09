<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('competition_submissions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('photographer_id')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('competition_submissions', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('category_id')->constrained('locations')->onDelete('set null');
            }
            if (!Schema::hasColumn('competition_submissions', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('vote_count');
            }
            if (!Schema::hasColumn('competition_submissions', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('rejection_reason');
            }
        });

        DB::statement("ALTER TABLE competition_submissions MODIFY COLUMN status ENUM('pending','payment_pending','pending_review','approved','rejected','disqualified') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            $columns = ['user_id', 'district_id', 'submitted_at', 'reject_reason'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('competition_submissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::statement("ALTER TABLE competition_submissions MODIFY COLUMN status ENUM('pending_review','approved','rejected','disqualified') DEFAULT 'pending_review'");
    }
};
