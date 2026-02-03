<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_mentor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('competition_id');
            $table->unsignedBigInteger('mentor_id');
            $table->enum('role_type', ['mentor', 'speaker', 'trainer'])->default('mentor');
            $table->text('note')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            $table->foreign('mentor_id')->references('id')->on('mentors')->onDelete('cascade');
            
            $table->unique(['competition_id', 'mentor_id']);
            $table->index(['competition_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_mentor');
    }
};
