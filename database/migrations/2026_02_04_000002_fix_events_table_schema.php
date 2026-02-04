<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix events table schema inconsistencies and add missing fields
     * for professional event/workshop platform
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add registration deadline (separate from booking_close_datetime)
            if (!Schema::hasColumn('events', 'registration_deadline')) {
                $table->dateTime('registration_deadline')
                    ->nullable()
                    ->comment('Last datetime users can register for event');
            }
            
            // Add certificate system fields
            if (!Schema::hasColumn('events', 'certificates_enabled')) {
                $table->boolean('certificates_enabled')
                    ->default(false)
                    ->comment('Auto-issue certificates after attendance');
            }
            
            if (!Schema::hasColumn('events', 'certificate_template_id')) {
                $table->foreignId('certificate_template_id')
                    ->nullable()
                    ->constrained('certificate_templates', 'id')
                    ->nullOnDelete();
            }
            
            // Add price field (clarify from ticket_price)
            if (!Schema::hasColumn('events', 'price')) {
                $table->decimal('price', 10, 2)
                    ->nullable()
                    ->comment('Event price (free if NULL). Same as ticket_price.');
            }
            
            // Add capacity field (standard term, clearer than max_attendees)
            if (!Schema::hasColumn('events', 'capacity')) {
                $table->integer('capacity')
                    ->nullable()
                    ->comment('Max attendees. Replaces max_attendees.');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'registration_deadline',
                'certificates_enabled',
                'certificate_template_id',
                'price',
                'capacity',
            ]);
        });
    }
};
