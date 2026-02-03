<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ticket_id')->nullable()->constrained('event_tickets')->onDelete('set null');
            $table->integer('qty')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('qr_token')->unique()->nullable();
            $table->enum('status', ['pending_payment', 'confirmed', 'cancelled', 'refunded', 'attended'])->default('confirmed');
            $table->timestamp('attended_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['event_id', 'user_id']);
            $table->index(['event_id', 'status']);
            $table->index('qr_token');
            $table->unique(['event_id', 'user_id', 'ticket_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_registrations');
    }
};
