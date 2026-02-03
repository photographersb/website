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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('transaction_type', ['booking', 'subscription', 'featured', 'competition_entry', 'refund', 'payout'])->index();
            $table->string('reference_id')->nullable(); // booking ID, subscription ID, etc.
            $table->string('reference_table')->nullable(); // bookings, subscriptions, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('BDT');
            $table->enum('payment_method', ['card', 'bkash', 'nagad', 'bank_transfer', 'manual'])->default('manual');
            $table->string('gateway_reference')->nullable(); // SSLCommerz ref, bKash ref, etc.
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->text('failure_reason')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'payment_date']);
            $table->index(['transaction_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
