<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->string('hero_image_credit_name')->nullable()->after('hero_image');
            $table->string('hero_image_credit_url')->nullable()->after('hero_image_credit_name');
            $table->string('banner_image_credit_name')->nullable()->after('banner_image');
            $table->string('banner_image_credit_url')->nullable()->after('banner_image_credit_name');
            $table->string('cover_image_credit_name')->nullable()->after('cover_image');
            $table->string('cover_image_credit_url')->nullable()->after('cover_image_credit_name');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn([
                'hero_image_credit_name',
                'hero_image_credit_url',
                'banner_image_credit_name',
                'banner_image_credit_url',
                'cover_image_credit_name',
                'cover_image_credit_url',
            ]);
        });
    }
};
