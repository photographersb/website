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
            $table->dropUnique('comp_submissions_unique');
            $table->index(['competition_id', 'photographer_id'], 'idx_submissions_competition_photographer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->dropIndex('idx_submissions_competition_photographer');
            $table->unique(['competition_id', 'photographer_id'], 'comp_submissions_unique');
        });
    }
};
