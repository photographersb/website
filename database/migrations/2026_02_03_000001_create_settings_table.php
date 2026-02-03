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
        if (Schema::hasTable('settings')) {
            return; // Table already exists, skip
        }
        
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Core fields
            $table->string('key')->unique()
                  ->comment('Setting key (snake_case)');
            
            $table->longText('value')->nullable()
                  ->comment('Setting value (JSON compatible)');
            
            $table->string('group')->default('general')
                  ->comment('Setting group: general, tracking, payment, email, storage, etc.');
            
            $table->string('data_type')->default('string')
                  ->comment('Data type: string, integer, boolean, json, array');
            
            $table->text('description')->nullable()
                  ->comment('What this setting controls');
            
            // Admin audit
            $table->boolean('is_public')->default(false)
                  ->comment('Can frontend fetch this setting?');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('group');
            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
