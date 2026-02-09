<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsors', 'website_url')) {
                $table->string('website_url')->nullable()->after('website');
            }
            if (!Schema::hasColumn('sponsors', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });

        DB::statement("UPDATE sponsors SET website_url = website WHERE website_url IS NULL AND website IS NOT NULL");
        DB::statement("UPDATE sponsors SET is_active = CASE WHEN status = 'active' THEN 1 ELSE 0 END");
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            if (Schema::hasColumn('sponsors', 'website_url')) {
                $table->dropColumn('website_url');
            }
            if (Schema::hasColumn('sponsors', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
