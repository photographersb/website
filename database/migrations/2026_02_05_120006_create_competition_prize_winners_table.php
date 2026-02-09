<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_prize_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_prize_id')->constrained('competition_prizes')->onDelete('cascade');
            $table->foreignId('submission_id')->constrained('competition_submissions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['competition_prize_id', 'submission_id'], 'prize_winners_prize_sub_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_prize_winners');
    }
};
