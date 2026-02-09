<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('submission_id')->constrained('competition_submissions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('method', ['bkash', 'nagad', 'rocket', 'manual']);
            $table->string('sender_number')->nullable();
            $table->string('trx_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('screenshot_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['competition_id', 'status']);
            $table->index(['submission_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_payments');
    }
};
