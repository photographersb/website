<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_judges', function (Blueprint $table) {
            // Add judge_profile_id to link to judges table
            $table->unsignedBigInteger('judge_profile_id')->nullable()->after('judge_id');
            $table->integer('sort_order')->default(0)->after('is_active');
            
            $table->foreign('judge_profile_id')->references('id')->on('judges')->onDelete('set null');
            $table->index(['competition_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('competition_judges', function (Blueprint $table) {
            $table->dropForeign(['judge_profile_id']);
            $table->dropColumn(['judge_profile_id', 'sort_order']);
        });
    }
};
