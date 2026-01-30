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
        Schema::create('competition_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->string('rank'); // 1st, 2nd, 3rd, etc
            $table->string('title'); // e.g., "Grand Prize", "First Place", "People's Choice"
            $table->text('description')->nullable();
            $table->decimal('cash_amount', 10, 2)->nullable();
            $table->text('physical_prizes')->nullable(); // JSON or text describing physical prizes
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('competition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_prizes');
    }
};
