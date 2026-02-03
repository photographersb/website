<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_sponsors', function (Blueprint $table) {
            $table->unsignedBigInteger('sponsor_id')->nullable()->after('competition_id');
            $table->foreign('sponsor_id')->references('id')->on('sponsors')->onDelete('set null');
            $table->index('sponsor_id', 'idx_competition_sponsors_sponsor_id');
            $table->unique(['competition_id', 'sponsor_id'], 'ux_competition_sponsors_competition_sponsor');
        });
    }

    public function down(): void
    {
        Schema::table('competition_sponsors', function (Blueprint $table) {
            $table->dropUnique('ux_competition_sponsors_competition_sponsor');
            $table->dropIndex('idx_competition_sponsors_sponsor_id');
            $table->dropForeign(['sponsor_id']);
            $table->dropColumn('sponsor_id');
        });
    }
};
