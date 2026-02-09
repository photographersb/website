<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('click_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->text('page_url');
            $table->string('route_name')->nullable();
            $table->string('element_tag', 64)->nullable();
            $table->string('element_id', 191)->nullable();
            $table->text('element_classes')->nullable();
            $table->text('element_text')->nullable();
            $table->string('element_name', 191)->nullable();
            $table->string('element_type', 64)->nullable();
            $table->text('input_value')->nullable();
            $table->unsignedInteger('click_x')->nullable();
            $table->unsignedInteger('click_y')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_events');
    }
};
