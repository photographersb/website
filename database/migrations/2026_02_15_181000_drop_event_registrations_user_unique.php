<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('event_registrations')) {
            $uxIndex = DB::select("SHOW INDEX FROM event_registrations WHERE Key_name = 'ux_event_registrations_user'");
            if (!empty($uxIndex)) {
                DB::statement('ALTER TABLE event_registrations DROP INDEX ux_event_registrations_user');
            }

            $legacyIndex = DB::select("SHOW INDEX FROM event_registrations WHERE Key_name = 'event_registrations_unique_event_user'");
            if (!empty($legacyIndex)) {
                DB::statement('ALTER TABLE event_registrations DROP INDEX event_registrations_unique_event_user');
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('event_registrations')) {
            DB::statement('ALTER TABLE event_registrations ADD UNIQUE ux_event_registrations_user (event_id, user_id)');
        }
    }
};
