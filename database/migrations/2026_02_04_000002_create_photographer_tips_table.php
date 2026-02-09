<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photographer_tips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('BDT');
            $table->enum('payment_method', ['bkash', 'nagad', 'rocket', 'manual'])->default('bkash');
            $table->string('transaction_id')->nullable()->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['photographer_id', 'status']);
            $table->index(['payment_method', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photographer_tips');
    }
};
