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
        Schema::table('photographers', function (Blueprint $table) {
            if (!Schema::hasColumn('photographers', 'share_code')) {
                $table->string('share_code', 32)->nullable()->unique()->after('slug');
            }
        });

        Schema::table('photographer_stats', function (Blueprint $table) {
            if (!Schema::hasColumn('photographer_stats', 'profile_share_clicks')) {
                $table->unsignedBigInteger('profile_share_clicks')->default(0)->after('profile_views_this_month');
            }
            if (!Schema::hasColumn('photographer_stats', 'profile_share_visits')) {
                $table->unsignedBigInteger('profile_share_visits')->default(0)->after('profile_share_clicks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            if (Schema::hasColumn('photographers', 'share_code')) {
                $table->dropUnique(['share_code']);
                $table->dropColumn('share_code');
            }
        });

        Schema::table('photographer_stats', function (Blueprint $table) {
            if (Schema::hasColumn('photographer_stats', 'profile_share_clicks')) {
                $table->dropColumn('profile_share_clicks');
            }
            if (Schema::hasColumn('photographer_stats', 'profile_share_visits')) {
                $table->dropColumn('profile_share_visits');
            }
        });
    }
};
