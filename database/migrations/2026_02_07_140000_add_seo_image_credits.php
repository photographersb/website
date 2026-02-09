<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('og_image_credit_name')->nullable()->after('og_image');
            $table->string('og_image_credit_url')->nullable()->after('og_image_credit_name');
            $table->string('twitter_image_credit_name')->nullable()->after('twitter_image');
            $table->string('twitter_image_credit_url')->nullable()->after('twitter_image_credit_name');
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->dropColumn([
                'og_image_credit_name',
                'og_image_credit_url',
                'twitter_image_credit_name',
                'twitter_image_credit_url',
            ]);
        });
    }
};
