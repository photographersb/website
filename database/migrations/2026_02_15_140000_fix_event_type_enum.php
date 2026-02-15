<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop and recreate enum columns with correct values
        DB::statement("ALTER TABLE events MODIFY event_type ENUM('workshop','photowalk','expo','seminar','meetup','webinar','exhibition','competition','other') DEFAULT 'other'");
        DB::statement("ALTER TABLE events MODIFY type ENUM('workshop','photowalk','expo','seminar','meetup','webinar','exhibition','competition','other') NULL");
        DB::statement("ALTER TABLE events MODIFY event_mode ENUM('free','paid') DEFAULT 'free'");
    }

    public function down(): void
    {
        // Revert to original enum values if needed
        DB::statement("ALTER TABLE events MODIFY event_type ENUM('workshop','exhibition','meetup','competition','seminar','other') DEFAULT 'other'");
    }
};
