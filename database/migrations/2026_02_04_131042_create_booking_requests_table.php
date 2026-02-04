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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // SB-BK-2026-0001
            $table->foreignId('client_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('photographer_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->unsignedBigInteger('city_id')->nullable(); // Manual FK to avoid constraint issues
            $table->string('venue_address')->nullable();
            $table->date('event_date')->nullable(); // Required but nullable for form flexibility
            $table->time('event_time')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('client_user_id');
            $table->index('photographer_user_id');
            $table->index('status');
            $table->index('event_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
