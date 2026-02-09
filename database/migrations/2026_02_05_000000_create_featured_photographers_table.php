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
        Schema::create('featured_photographers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            $table->enum('package_tier', ['Starter', 'Professional', 'Enterprise']);
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('active')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('photographer_id');
            $table->index('package_tier');
            $table->index('category');
            $table->index('location');
            $table->index('active');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['active', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_photographers');
    }
};
