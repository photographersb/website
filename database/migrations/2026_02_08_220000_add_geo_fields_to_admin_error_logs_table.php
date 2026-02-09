<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_error_logs')) {
            return;
        }

        Schema::table('admin_error_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_error_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip');
            }
            if (!Schema::hasColumn('admin_error_logs', 'geo_country')) {
                $table->string('geo_country', 100)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('admin_error_logs', 'geo_region')) {
                $table->string('geo_region', 100)->nullable()->after('geo_country');
            }
            if (!Schema::hasColumn('admin_error_logs', 'geo_city')) {
                $table->string('geo_city', 100)->nullable()->after('geo_region');
            }
            if (!Schema::hasColumn('admin_error_logs', 'geo_lat')) {
                $table->decimal('geo_lat', 10, 7)->nullable()->after('geo_city');
            }
            if (!Schema::hasColumn('admin_error_logs', 'geo_lng')) {
                $table->decimal('geo_lng', 10, 7)->nullable()->after('geo_lat');
            }
            if (!Schema::hasColumn('admin_error_logs', 'geo_timezone')) {
                $table->string('geo_timezone', 60)->nullable()->after('geo_lng');
            }
            if (!Schema::hasColumn('admin_error_logs', 'geo_isp')) {
                $table->string('geo_isp', 120)->nullable()->after('geo_timezone');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('admin_error_logs')) {
            return;
        }

        Schema::table('admin_error_logs', function (Blueprint $table) {
            if (Schema::hasColumn('admin_error_logs', 'geo_isp')) {
                $table->dropColumn('geo_isp');
            }
            if (Schema::hasColumn('admin_error_logs', 'geo_timezone')) {
                $table->dropColumn('geo_timezone');
            }
            if (Schema::hasColumn('admin_error_logs', 'geo_lng')) {
                $table->dropColumn('geo_lng');
            }
            if (Schema::hasColumn('admin_error_logs', 'geo_lat')) {
                $table->dropColumn('geo_lat');
            }
            if (Schema::hasColumn('admin_error_logs', 'geo_city')) {
                $table->dropColumn('geo_city');
            }
            if (Schema::hasColumn('admin_error_logs', 'geo_region')) {
                $table->dropColumn('geo_region');
            }
            if (Schema::hasColumn('admin_error_logs', 'geo_country')) {
                $table->dropColumn('geo_country');
            }
            if (Schema::hasColumn('admin_error_logs', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
        });
    }
};
