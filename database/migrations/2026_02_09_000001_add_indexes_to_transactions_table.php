<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'reference_id')) {
                return;
            }

            $table->index('reference_id', 'idx_transactions_reference_id');
            $table->index('transaction_id', 'idx_transactions_transaction_id');
            $table->index('booking_id', 'idx_transactions_booking_id');
            $table->index('photographer_id', 'idx_transactions_photographer_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_reference_id');
            $table->dropIndex('idx_transactions_transaction_id');
            $table->dropIndex('idx_transactions_booking_id');
            $table->dropIndex('idx_transactions_photographer_id');
        });
    }
};
