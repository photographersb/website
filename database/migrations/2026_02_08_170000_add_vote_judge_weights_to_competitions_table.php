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
        Schema::table('competitions', function (Blueprint $table) {
            $table->decimal('vote_weight', 5, 2)->default(0.40)->after('allow_judge_scoring');
            $table->decimal('judge_weight', 5, 2)->default(0.60)->after('vote_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn(['vote_weight', 'judge_weight']);
        });
    }
};
