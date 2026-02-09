<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('competition_submissions', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')
                    ->nullable()
                    ->after('submitted_at')
                    ->comment('When the submitter accepted competition terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('competition_submissions', 'terms_accepted_at')) {
                $table->dropColumn('terms_accepted_at');
            }
        });
    }
};
