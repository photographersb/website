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
        Schema::table('events', function (Blueprint $table) {
            $table->enum('event_type', ['workshop', 'exhibition', 'meetup', 'competition', 'seminar', 'other'])
                ->default('other')
                ->after('description');
            $table->text('requirements')->nullable()->after('is_verified');
            $table->decimal('duration_hours', 5, 2)->nullable()->after('requirements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'requirements', 'duration_hours']);
        });
    }
};
