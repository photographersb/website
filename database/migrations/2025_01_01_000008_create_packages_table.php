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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // Engagement, Wedding Venue, Reception, etc.
            $table->decimal('base_price', 10, 2);
            $table->enum('duration_unit', ['hours', 'days', 'events'])->default('hours');
            $table->integer('duration_value')->default(1);
            $table->json('includes')->nullable(); // Array of included items
            $table->json('excludes')->nullable(); // Array of excluded items
            $table->json('add_ons')->nullable(); // Array of add-on items with prices
            $table->enum('travel_cost_type', ['fixed', 'per_km', 'free'])->default('free');
            $table->decimal('travel_cost_value', 10, 2)->nullable();
            $table->integer('advance_booking_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('view_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->integer('booking_count')->default(0);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['photographer_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
