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
        Schema::create('competition_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Portrait", "Landscape", "Wildlife"
            $table->text('description')->nullable();
            $table->decimal('prize_amount', 10, 2)->nullable();
            $table->integer('max_submissions_per_user')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('submission_count')->default(0);
            $table->timestamps();
            
            $table->index('competition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_categories');
    }
};
