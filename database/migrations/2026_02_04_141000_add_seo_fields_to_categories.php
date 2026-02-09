<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('categories', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('categories', 'seo_keywords')) {
                $table->text('seo_keywords')->nullable()->after('seo_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'seo_keywords']);
        });
    }
};
