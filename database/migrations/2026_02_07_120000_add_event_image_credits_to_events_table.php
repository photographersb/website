<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('hero_image_credit_name')->nullable()->after('hero_image_url');
            $table->string('hero_image_credit_url')->nullable()->after('hero_image_credit_name');
            $table->string('banner_image_credit_name')->nullable()->after('banner_image');
            $table->string('banner_image_credit_url')->nullable()->after('banner_image_credit_name');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'hero_image_credit_name',
                'hero_image_credit_url',
                'banner_image_credit_name',
                'banner_image_credit_url',
            ]);
        });
    }
};
