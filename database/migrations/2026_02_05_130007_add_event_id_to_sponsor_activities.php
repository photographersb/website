<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsor_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsor_activities', 'event_id')) {
                $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null')->after('competition_id');
                $table->index(['event_id', 'type']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('sponsor_activities', function (Blueprint $table) {
            if (Schema::hasColumn('sponsor_activities', 'event_id')) {
                $table->dropColumn('event_id');
            }
        });
    }
};
