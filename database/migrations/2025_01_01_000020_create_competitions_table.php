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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('admin_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('organizer_id')->nullable()->constrained('photographers')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('theme')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->dateTime('submission_deadline');
            $table->dateTime('voting_start_at')->nullable();
            $table->dateTime('voting_end_at')->nullable();
            $table->dateTime('judging_start_at')->nullable();
            $table->dateTime('judging_end_at')->nullable();
            $table->date('results_announcement_date')->nullable();
            $table->enum('status', ['draft', 'active', 'judging', 'completed', 'cancelled'])->default('draft');
            $table->boolean('allow_public_voting')->default(true);
            $table->boolean('allow_judge_scoring')->default(true);
            $table->boolean('allow_watermark')->default(false);
            $table->boolean('require_watermark')->default(false);
            $table->decimal('participation_fee', 10, 2)->default(0);
            $table->boolean('is_paid_competition')->default(false);
            $table->integer('max_submissions_per_user')->default(5);
            $table->integer('min_submissions_to_proceed')->default(10);
            $table->decimal('total_prize_pool', 10, 2)->nullable();
            $table->integer('number_of_winners')->default(3);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->integer('total_submissions')->default(0);
            $table->integer('total_votes')->default(0);
            $table->boolean('results_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'submission_deadline']);
            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
