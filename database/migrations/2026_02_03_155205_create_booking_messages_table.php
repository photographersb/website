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
        if (Schema::hasTable('booking_messages')) {
            return;
        }

        Schema::create('booking_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->enum('sender_type', ['client', 'photographer']); // client or photographer
            $table->text('message');
            $table->json('attachments')->nullable(); // [{filename, url, type, size}]
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_system_message')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index(['booking_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
            $table->index(['is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_messages');
    }
};
