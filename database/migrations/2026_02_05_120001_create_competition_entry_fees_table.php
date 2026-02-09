<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_entry_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->enum('user_type', ['guest', 'registered', 'verified', 'student']);
            $table->decimal('fee_amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->timestamps();

            $table->unique(['competition_id', 'user_type'], 'ux_competition_entry_fee');
            $table->index(['competition_id', 'user_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_entry_fees');
    }
};
