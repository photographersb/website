<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained('sponsors')->onDelete('cascade');
            $table->foreignId('competition_id')->nullable()->constrained('competitions')->onDelete('set null');
            $table->enum('type', ['impression', 'click']);
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['sponsor_id', 'type']);
            $table->index(['competition_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_activities');
    }
};
