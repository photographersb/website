<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'risk_score')) {
                $table->unsignedTinyInteger('risk_score')->default(0)->after('last_login_ip');
                $table->index('risk_score');
            }

            if (!Schema::hasColumn('users', 'trust_score')) {
                $table->unsignedTinyInteger('trust_score')->default(0)->after('risk_score');
                $table->index('trust_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'trust_score')) {
                $table->dropIndex(['trust_score']);
                $table->dropColumn('trust_score');
            }

            if (Schema::hasColumn('users', 'risk_score')) {
                $table->dropIndex(['risk_score']);
                $table->dropColumn('risk_score');
            }
        });
    }
};
