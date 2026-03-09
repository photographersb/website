<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('certificate_templates')) {
            try {
                DB::statement("ALTER TABLE certificate_templates MODIFY type VARCHAR(50) NOT NULL");
            } catch (\Throwable $e) {
                // Ignore if database engine does not support this alteration.
            }
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('certificates', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('certificate_code');
            }

            if (!Schema::hasColumn('certificates', 'issued_at')) {
                $table->timestamp('issued_at')->nullable()->after('recipient_name');
            }

            if (!Schema::hasColumn('certificates', 'certificate_path')) {
                $table->string('certificate_path')->nullable()->after('issued_at');
            }

            if (!Schema::hasColumn('certificates', 'issued_by_user_id')) {
                $table->foreignId('issued_by_user_id')->nullable()->after('created_by_user_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('certificates', 'notes')) {
                $table->text('notes')->nullable()->after('issued_by_user_id');
            }
        });

        if (!Schema::hasTable('certificate_logs')) {
            Schema::create('certificate_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('certificate_id')->nullable()->constrained('certificates')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action_type');
                $table->string('entity_type')->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->text('message')->nullable();
                $table->string('rule_triggered')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['action_type', 'created_at']);
                $table->index(['entity_type', 'entity_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('certificate_logs')) {
            Schema::dropIfExists('certificate_logs');
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('certificates', 'issued_by_user_id')) {
                $table->dropConstrainedForeignId('issued_by_user_id');
            }

            if (Schema::hasColumn('certificates', 'certificate_path')) {
                $table->dropColumn('certificate_path');
            }

            if (Schema::hasColumn('certificates', 'issued_at')) {
                $table->dropColumn('issued_at');
            }

            if (Schema::hasColumn('certificates', 'recipient_name')) {
                $table->dropColumn('recipient_name');
            }

            if (Schema::hasColumn('certificates', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
