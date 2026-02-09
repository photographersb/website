<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'type')) {
                $table->enum('type', ['workshop', 'photowalk', 'expo', 'seminar', 'meetup', 'webinar'])
                    ->default('workshop')
                    ->after('slug');
            }
            if (!Schema::hasColumn('events', 'venue_name')) {
                $table->string('venue_name')->nullable()->after('location');
            }
            if (!Schema::hasColumn('events', 'venue_address')) {
                $table->text('venue_address')->nullable()->after('venue_name');
            }
            if (!Schema::hasColumn('events', 'google_map_link')) {
                $table->string('google_map_link')->nullable()->after('venue_address');
            }
            if (!Schema::hasColumn('events', 'start_datetime')) {
                $table->dateTime('start_datetime')->nullable()->after('event_date');
            }
            if (!Schema::hasColumn('events', 'end_datetime')) {
                $table->dateTime('end_datetime')->nullable()->after('event_end_date');
            }
            if (!Schema::hasColumn('events', 'event_mode')) {
                $table->enum('event_mode', ['free', 'paid'])->default('free')->after('status');
            }
            if (!Schema::hasColumn('events', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('event_mode');
            }
            if (!Schema::hasColumn('events', 'currency')) {
                $table->string('currency', 10)->default('BDT')->after('price');
            }
            if (!Schema::hasColumn('events', 'capacity')) {
                $table->integer('capacity')->nullable()->after('max_attendees');
            }
            if (!Schema::hasColumn('events', 'max_tickets_per_user')) {
                $table->integer('max_tickets_per_user')->default(1)->after('capacity');
            }
            if (!Schema::hasColumn('events', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('hero_image_url');
            }
            if (!Schema::hasColumn('events', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('banner_image');
            }
            if (!Schema::hasColumn('events', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('certificate_template_id');
            }
            if (!Schema::hasColumn('events', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('events', 'og_image')) {
                $table->string('og_image')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('events', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('organizer_id');
            }
        });

        // Update status enum to include closed
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','published','closed','cancelled','completed') DEFAULT 'draft'");

        // Align city_id to locations if possible
        if (Schema::hasColumn('events', 'city_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign(['city_id']);
            });
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('city_id')->references('id')->on('locations')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $columns = [
                'type',
                'venue_name',
                'venue_address',
                'google_map_link',
                'start_datetime',
                'end_datetime',
                'event_mode',
                'price',
                'currency',
                'capacity',
                'max_tickets_per_user',
                'banner_image',
                'gallery_images',
                'meta_title',
                'meta_description',
                'og_image',
                'created_by',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','published','completed','cancelled') DEFAULT 'draft'");
    }
};
