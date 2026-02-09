<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['scanner', 'manager'])->default('scanner');
            $table->timestamps();

            $table->unique(['event_id', 'user_id'], 'ux_event_staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_staff');
    }
};
