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
        Schema::table('competition_sponsors', function (Blueprint $table) {
            // Change logo_url from VARCHAR(255) to TEXT to accommodate base64 encoded images
            $table->text('logo_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_sponsors', function (Blueprint $table) {
            // Revert back to string (VARCHAR)
            $table->string('logo_url')->nullable()->change();
        });
    }
};
