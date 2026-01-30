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
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->default(0)->change();
            $table->enum('duration_unit', ['hours', 'days', 'events'])->default('hours')->change();
            $table->integer('duration_value')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->nullable()->change();
            $table->enum('duration_unit', ['hours', 'days', 'events'])->nullable(false)->change();
            $table->integer('duration_value')->nullable(false)->change();
        });
    }
};
