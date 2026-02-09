<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_photographer_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_photographer_id')
                ->constrained('featured_photographers')
                ->onDelete('cascade');
            $table->enum('from_package', ['Starter', 'Professional', 'Enterprise'])->default('Starter');
            $table->enum('to_package', ['Starter', 'Professional', 'Enterprise']);
            $table->decimal('prorated_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', [
                'bkash',
                'nagad',
                'upay',
                'sslcommerz',
                'cash',
            ]);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('reference_number')->nullable();
            $table->timestamp('upgraded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('featured_photographer_id', 'fp_upgrades_fp_id_idx');
            $table->index(['featured_photographer_id', 'payment_status'], 'fp_upgrades_fp_id_status_idx');
            $table->index(['payment_method', 'created_at'], 'fp_upgrades_method_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_photographer_upgrades');
    }
};
