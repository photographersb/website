<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'action_type')) {
                $table->string('action_type')->nullable()->after('action');
                $table->index('action_type');
            }

            if (!Schema::hasColumn('activity_logs', 'entity_type')) {
                $table->string('entity_type')->nullable()->after('model_type');
            }

            if (!Schema::hasColumn('activity_logs', 'entity_id')) {
                $table->unsignedBigInteger('entity_id')->nullable()->after('model_id');
                $table->index(['entity_type', 'entity_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'action_type')) {
                $table->dropIndex(['action_type']);
                $table->dropColumn('action_type');
            }

            if (Schema::hasColumn('activity_logs', 'entity_id')) {
                $table->dropIndex(['entity_type', 'entity_id']);
                $table->dropColumn('entity_id');
            }

            if (Schema::hasColumn('activity_logs', 'entity_type')) {
                $table->dropColumn('entity_type');
            }
        });
    }
};
