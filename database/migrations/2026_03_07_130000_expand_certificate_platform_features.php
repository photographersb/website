<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificate_templates', 'background_image')) {
                $table->string('background_image')->nullable()->after('height');
            }

            if (!Schema::hasColumn('certificate_templates', 'font_family')) {
                $table->string('font_family')->default('serif')->after('text_color');
            }

            if (!Schema::hasColumn('certificate_templates', 'font_size')) {
                $table->unsignedInteger('font_size')->default(28)->after('font_family');
            }
        });

        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'source_type')) {
                $table->string('source_type')->nullable()->after('competition_id');
            }

            if (!Schema::hasColumn('certificates', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
                $table->index(['source_type', 'source_id']);
            }

            if (!Schema::hasColumn('certificates', 'png_path')) {
                $table->string('png_path')->nullable()->after('certificate_path');
            }

            if (!Schema::hasColumn('certificates', 'share_image_paths')) {
                $table->json('share_image_paths')->nullable()->after('png_path');
            }

            if (!Schema::hasColumn('certificates', 'share_message')) {
                $table->string('share_message')->nullable()->after('share_image_paths');
            }
        });

        if (!Schema::hasTable('certificate_automation_rules')) {
            Schema::create('certificate_automation_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('trigger_type', [
                    'event_attendance',
                    'workshop_completed',
                    'competition_winners_announced',
                    'participation_confirmed',
                ]);
                $table->enum('source_type', ['event', 'workshop', 'competition', 'award', 'participation']);
                $table->unsignedBigInteger('source_id')->nullable();
                $table->foreignId('template_id')->constrained('certificate_templates')->cascadeOnDelete();
                $table->boolean('is_active')->default(true);
                $table->json('config')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['trigger_type', 'source_type', 'source_id'], 'cert_auto_rule_scope_idx');
                $table->index(['is_active', 'template_id'], 'cert_auto_rule_active_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('certificate_automation_rules')) {
            Schema::dropIfExists('certificate_automation_rules');
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'share_message')) {
                $table->dropColumn('share_message');
            }
            if (Schema::hasColumn('certificates', 'share_image_paths')) {
                $table->dropColumn('share_image_paths');
            }
            if (Schema::hasColumn('certificates', 'png_path')) {
                $table->dropColumn('png_path');
            }
            if (Schema::hasColumn('certificates', 'source_id')) {
                $table->dropIndex('certificates_source_type_source_id_index');
                $table->dropColumn('source_id');
            }
            if (Schema::hasColumn('certificates', 'source_type')) {
                $table->dropColumn('source_type');
            }
        });

        Schema::table('certificate_templates', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_templates', 'font_size')) {
                $table->dropColumn('font_size');
            }
            if (Schema::hasColumn('certificate_templates', 'font_family')) {
                $table->dropColumn('font_family');
            }
            if (Schema::hasColumn('certificate_templates', 'background_image')) {
                $table->dropColumn('background_image');
            }
        });
    }
};
