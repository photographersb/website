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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->string('thumbnail_url');
            $table->string('camera_make')->nullable();
            $table->string('camera_model')->nullable();
            $table->json('camera_settings')->nullable(); // ISO, aperture, shutter speed, etc.
            $table->string('location')->nullable();
            $table->date('date_taken')->nullable();
            $table->integer('display_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['photographer_id', 'album_id']);
            $table->index(['date_taken']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
