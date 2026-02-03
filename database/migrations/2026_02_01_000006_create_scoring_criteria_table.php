<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scoring_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('competition_id');
            $table->string('title'); // e.g., Composition, Lighting, Storytelling
            $table->string('description')->nullable();
            $table->integer('max_score')->default(10);
            $table->decimal('weight', 5, 2)->default(1.0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            $table->index(['competition_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scoring_criteria');
    }
};
