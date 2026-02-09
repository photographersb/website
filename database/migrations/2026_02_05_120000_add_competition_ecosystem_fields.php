<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            if (!Schema::hasColumn('competitions', 'mode')) {
                $table->enum('mode', ['open', 'pro', 'student', 'district_battle'])->default('open')->after('status');
            }
            if (!Schema::hasColumn('competitions', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('banner_image');
            }
            if (!Schema::hasColumn('competitions', 'start_date')) {
                $table->date('start_date')->nullable()->after('banner_image');
            }
            if (!Schema::hasColumn('competitions', 'end_date')) {
                $table->date('end_date')->nullable()->after('submission_deadline');
            }
            if (!Schema::hasColumn('competitions', 'judging_deadline')) {
                $table->date('judging_deadline')->nullable()->after('judging_end_at');
            }
            if (!Schema::hasColumn('competitions', 'announcement_date')) {
                $table->date('announcement_date')->nullable()->after('results_announcement_date');
            }
            if (!Schema::hasColumn('competitions', 'entry_type')) {
                $table->enum('entry_type', ['single', 'series', 'both'])->default('single')->after('max_submissions_per_user');
            }
            if (!Schema::hasColumn('competitions', 'series_min_images')) {
                $table->unsignedSmallInteger('series_min_images')->nullable()->after('entry_type');
            }
            if (!Schema::hasColumn('competitions', 'series_max_images')) {
                $table->unsignedSmallInteger('series_max_images')->nullable()->after('series_min_images');
            }
            if (!Schema::hasColumn('competitions', 'voting_enabled')) {
                $table->boolean('voting_enabled')->default(false)->after('allow_public_voting');
            }
            if (!Schema::hasColumn('competitions', 'voting_start_date')) {
                $table->dateTime('voting_start_date')->nullable()->after('voting_start_at');
            }
            if (!Schema::hasColumn('competitions', 'voting_end_date')) {
                $table->dateTime('voting_end_date')->nullable()->after('voting_end_at');
            }
            if (!Schema::hasColumn('competitions', 'district_battle_enabled')) {
                $table->boolean('district_battle_enabled')->default(false)->after('voting_enabled');
            }
        });

        DB::statement("ALTER TABLE competitions MODIFY COLUMN status ENUM('draft','published','closed','active','judging','completed','cancelled','archived') DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $columns = [
                'mode',
                'cover_image',
                'start_date',
                'end_date',
                'judging_deadline',
                'announcement_date',
                'entry_type',
                'series_min_images',
                'series_max_images',
                'voting_enabled',
                'voting_start_date',
                'voting_end_date',
                'district_battle_enabled',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('competitions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::statement("ALTER TABLE competitions MODIFY COLUMN status ENUM('draft','active','judging','completed','cancelled','archived') DEFAULT 'draft'");
    }
};
