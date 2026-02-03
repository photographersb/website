<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // If judge is platform user
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title')->nullable(); // Designation
            $table->longText('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('email')->nullable();
            $table->string('organization')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('website_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['is_active', 'sort_order']);
            $table->index('slug');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judges');
    }
};
