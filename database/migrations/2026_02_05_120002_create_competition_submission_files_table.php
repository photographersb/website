<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('competition_submissions')->onDelete('cascade');
            $table->string('image_path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('exif_json')->nullable();
            $table->timestamps();

            $table->index(['submission_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_submission_files');
    }
};
