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
        Schema::create('featured_photographer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_photographer_id')->constrained('featured_photographers')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['bkash', 'manual', 'bank_transfer']);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled']);
            $table->string('transaction_id')->nullable()->unique();
            $table->string('reference_number')->nullable();
            $table->json('payment_details')->nullable(); // Store payment response
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('featured_photographer_id');
            $table->index('payment_method');
            $table->index('status');
            $table->index('created_at');
            $table->index(['status', 'payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_photographer_payments');
    }
};
