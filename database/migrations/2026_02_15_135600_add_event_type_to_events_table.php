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
            // Add event type columns
            if (!Schema::hasColumn('events', 'event_type')) {
                $table->enum('event_type', ['workshop', 'photowalk', 'expo', 'seminar', 'meetup', 'webinar', 'exhibition', 'competition', 'other'])->nullable()->after('slug');
            }
            if (!Schema::hasColumn('events', 'type')) {
                $table->enum('type', ['workshop', 'photowalk', 'expo', 'seminar', 'meetup', 'webinar', 'exhibition', 'competition', 'other'])->nullable()->after('event_type');
            }
            
            // Add venue columns
            if (!Schema::hasColumn('events', 'venue_name')) {
                $table->string('venue_name')->nullable()->after('location');
            }
            if (!Schema::hasColumn('events', 'venue_address')) {
                $table->text('venue_address')->nullable()->after('venue_name');
            }
            if (!Schema::hasColumn('events', 'google_map_link')) {
                $table->text('google_map_link')->nullable()->after('venue_address');
            }
            
            // Add duration and timing
            if (!Schema::hasColumn('events', 'duration_hours')) {
                $table->decimal('duration_hours', 5, 2)->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('events', 'registration_deadline')) {
                $table->dateTime('registration_deadline')->nullable()->after('duration_hours');
            }
            
            // Add certificate columns
            if (!Schema::hasColumn('events', 'certificates_enabled')) {
                $table->boolean('certificates_enabled')->default(false)->after('registration_deadline');
            }
            if (!Schema::hasColumn('events', 'certificate_template_id')) {
                $table->foreignId('certificate_template_id')->nullable()->constrained('certificate_templates')->onDelete('set null')->after('certificates_enabled');
            }
            
            // Add image credit columns
            if (!Schema::hasColumn('events', 'hero_image_credit_name')) {
                $table->string('hero_image_credit_name')->nullable()->after('hero_image_url');
            }
            if (!Schema::hasColumn('events', 'hero_image_credit_url')) {
                $table->string('hero_image_credit_url')->nullable()->after('hero_image_credit_name');
            }
            if (!Schema::hasColumn('events', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('hero_image_credit_url');
            }
            if (!Schema::hasColumn('events', 'banner_image_credit_name')) {
                $table->string('banner_image_credit_name')->nullable()->after('banner_image');
            }
            if (!Schema::hasColumn('events', 'banner_image_credit_url')) {
                $table->string('banner_image_credit_url')->nullable()->after('banner_image_credit_name');
            }
            
            // Add gallery
            if (!Schema::hasColumn('events', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('banner_image_credit_url');
            }
            
            // Add pricing/event mode
            if (!Schema::hasColumn('events', 'event_mode')) {
                $table->enum('event_mode', ['free', 'paid'])->default('free')->after('is_ticketed');
            }
            if (!Schema::hasColumn('events', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('ticket_price');
            }
            if (!Schema::hasColumn('events', 'currency')) {
                $table->string('currency')->default('BDT')->after('price');
            }
            
            // Add capacity
            if (!Schema::hasColumn('events', 'capacity')) {
                $table->integer('capacity')->nullable()->after('max_attendees');
            }
            
            // Add tickets per user
            if (!Schema::hasColumn('events', 'max_tickets_per_user')) {
                $table->integer('max_tickets_per_user')->default(1)->after('capacity');
            }
            
            // Add requirements
            if (!Schema::hasColumn('events', 'requirements')) {
                $table->text('requirements')->nullable()->after('max_tickets_per_user');
            }
            
            // Add SEO columns
            if (!Schema::hasColumn('events', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('requirements');
            }
            if (!Schema::hasColumn('events', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('events', 'og_image')) {
                $table->string('og_image')->nullable()->after('meta_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop columns in reverse order of dependencies
            $columns = [
                'og_image', 'meta_description', 'meta_title',
                'requirements', 'max_tickets_per_user', 'capacity',
                'currency', 'price', 'event_mode',
                'gallery_images', 'banner_image_credit_url', 'banner_image_credit_name',
                'banner_image', 'hero_image_credit_url', 'hero_image_credit_name',
                'certificate_template_id', 'certificates_enabled',
                'registration_deadline', 'duration_hours',
                'google_map_link', 'venue_address', 'venue_name',
                'type', 'event_type'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
