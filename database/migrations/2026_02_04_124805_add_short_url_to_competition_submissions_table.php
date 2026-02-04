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
            $table->string('short_url')->nullable()->unique()->after('status');
            $table->string('share_token')->nullable()->unique()->after('short_url');
            $table->index('short_url');
            $table->index('share_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->dropIndex(['short_url']);
            $table->dropIndex(['share_token']);
            $table->dropColumn(['short_url', 'share_token']);
        });
    }
};
