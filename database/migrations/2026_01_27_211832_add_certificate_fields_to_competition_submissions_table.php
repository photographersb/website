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
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->string('certificate_id')->nullable()->unique()->after('winner_position');
            $table->string('certificate_url')->nullable()->after('certificate_id');
            $table->timestamp('certificate_generated_at')->nullable()->after('certificate_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->dropColumn(['certificate_id', 'certificate_url', 'certificate_generated_at']);
        });
    }
};
