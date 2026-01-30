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
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->decimal('prize_amount', 10, 2)->nullable()->after('certificate_generated_at');
            $table->text('prize_description')->nullable()->after('prize_amount');
            $table->enum('prize_status', ['pending', 'processing', 'delivered', 'claimed'])->default('pending')->after('prize_description');
            $table->timestamp('prize_delivered_at')->nullable()->after('prize_status');
            $table->text('prize_notes')->nullable()->after('prize_delivered_at');
            $table->string('tracking_number')->nullable()->after('prize_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'prize_amount',
                'prize_description',
                'prize_status',
                'prize_delivered_at',
                'prize_notes',
                'tracking_number'
            ]);
        });
    }
};
