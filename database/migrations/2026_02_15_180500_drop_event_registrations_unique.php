<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique('event_registrations_event_id_user_id_ticket_id_unique');
        });
    }

    public function down()
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'user_id', 'ticket_id']);
        });
    }
};
