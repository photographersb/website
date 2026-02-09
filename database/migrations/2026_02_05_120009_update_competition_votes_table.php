<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_votes', function (Blueprint $table) {
            if (!Schema::hasColumn('competition_votes', 'voter_user_id')) {
                $table->unsignedBigInteger('voter_user_id')->nullable()->after('voter_id');
                $table->index(['submission_id', 'voter_user_id'], 'idx_votes_submission_voter_user');
            }
            if (!Schema::hasColumn('competition_votes', 'ip')) {
                $table->string('ip')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('competition_votes', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('device_fingerprint');
            }
        });
    }

    public function down(): void
    {
        Schema::table('competition_votes', function (Blueprint $table) {
            if (Schema::hasColumn('competition_votes', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('competition_votes', 'ip')) {
                $table->dropColumn('ip');
            }
            if (Schema::hasColumn('competition_votes', 'voter_user_id')) {
                $table->dropIndex('idx_votes_submission_voter_user');
                $table->dropColumn('voter_user_id');
            }
        });
    }
};
