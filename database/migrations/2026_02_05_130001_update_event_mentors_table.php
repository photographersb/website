<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_mentors', function (Blueprint $table) {
            if (!Schema::hasColumn('event_mentors', 'role')) {
                $table->enum('role', ['mentor', 'speaker', 'guest', 'trainer'])->default('mentor')->after('mentor_id');
            }
            if (!Schema::hasColumn('event_mentors', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_mentors', function (Blueprint $table) {
            if (Schema::hasColumn('event_mentors', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('event_mentors', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
