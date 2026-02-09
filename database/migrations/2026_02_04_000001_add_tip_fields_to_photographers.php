<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            // Add tip/donation fields
            $table->string('bkash_number')->nullable()->comment('bKash number for receiving tips');
            $table->string('phone_number')->nullable()->comment('Alternative phone number');
            $table->boolean('accept_tips')->default(true)->comment('Allow photographers to receive tips');
            $table->string('tip_message')->nullable()->comment('Custom message for tip button');
        });
    }

    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->dropColumn(['bkash_number', 'phone_number', 'accept_tips', 'tip_message']);
        });
    }
};
