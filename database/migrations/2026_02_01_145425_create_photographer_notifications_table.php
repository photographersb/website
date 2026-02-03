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
        Schema::create('photographer_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            $table->string('type'); // booking_received, review_posted, competition_result, event_reminder, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data (booking_id, competition_id, etc.)
            $table->string('action_url')->nullable(); // Link to relevant page
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['photographer_id', 'is_read']);
            $table->index(['photographer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographer_notifications');
    }
};
