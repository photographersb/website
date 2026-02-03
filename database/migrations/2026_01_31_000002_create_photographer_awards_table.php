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
        Schema::create('photographer_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('organization')->nullable();
            $table->year('year');
            $table->text('description')->nullable();
            $table->string('certificate_url')->nullable(); // Optional: store certificate image
            $table->enum('type', ['award', 'achievement', 'recognition', 'certification'])->default('award');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index(['photographer_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographer_awards');
    }
};
