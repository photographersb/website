<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('sponsor_id')->constrained('sponsors')->onDelete('cascade');
            $table->enum('tier', ['title', 'gold', 'silver', 'bronze', 'support'])->default('bronze');
            $table->unsignedInteger('sort_order')->default(0);
            $table->decimal('sponsored_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'sponsor_id'], 'ux_event_sponsors');
            $table->index(['event_id', 'tier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sponsors');
    }
};
