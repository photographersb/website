<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds critical indexes identified in performance audit
     */
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'status') && Schema::hasColumn('events', 'published_at')
                    && !$this->indexExists('events', 'idx_events_status_published')) {
                    $table->index(['status', 'published_at'], 'idx_events_status_published');
                }
                if (Schema::hasColumn('events', 'user_id') && Schema::hasColumn('events', 'status')
                    && !$this->indexExists('events', 'idx_events_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_events_user_status');
                }
                if (Schema::hasColumn('events', 'start_date') && Schema::hasColumn('events', 'end_date')
                    && !$this->indexExists('events', 'idx_events_date_range')) {
                    $table->index(['start_date', 'end_date'], 'idx_events_date_range');
                }
                if (Schema::hasColumn('events', 'category_id')
                    && !$this->indexExists('events', 'idx_events_category_id')) {
                    $table->index('category_id', 'idx_events_category_id');
                }
            });
        }

        if (Schema::hasTable('event_tickets')) {
            Schema::table('event_tickets', function (Blueprint $table) {
                if (Schema::hasColumn('event_tickets', 'event_id') && Schema::hasColumn('event_tickets', 'ticket_type')
                    && !$this->indexExists('event_tickets', 'idx_event_tickets_type')) {
                    $table->index(['event_id', 'ticket_type'], 'idx_event_tickets_type');
                }
            });
        }

        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (Schema::hasColumn('event_registrations', 'user_id') && Schema::hasColumn('event_registrations', 'status')
                    && !$this->indexExists('event_registrations', 'idx_event_reg_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_event_reg_user_status');
                }
                if (Schema::hasColumn('event_registrations', 'ticket_id') && Schema::hasColumn('event_registrations', 'status')
                    && !$this->indexExists('event_registrations', 'idx_event_reg_ticket_status')) {
                    $table->index(['ticket_id', 'status'], 'idx_event_reg_ticket_status');
                }
                if (Schema::hasColumn('event_registrations', 'created_at') && Schema::hasColumn('event_registrations', 'status')
                    && !$this->indexExists('event_registrations', 'idx_event_reg_created_status')) {
                    $table->index(['created_at', 'status'], 'idx_event_reg_created_status');
                }
            });
        }

        if (Schema::hasTable('event_payments')) {
            Schema::table('event_payments', function (Blueprint $table) {
                if (Schema::hasColumn('event_payments', 'user_id') && Schema::hasColumn('event_payments', 'status')
                    && !$this->indexExists('event_payments', 'idx_event_payments_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_event_payments_user_status');
                }
                if (Schema::hasColumn('event_payments', 'event_id') && Schema::hasColumn('event_payments', 'status')
                    && !$this->indexExists('event_payments', 'idx_event_payments_event_status')) {
                    $table->index(['event_id', 'status'], 'idx_event_payments_event_status');
                }
                if (Schema::hasColumn('event_payments', 'verification_status')
                    && !$this->indexExists('event_payments', 'idx_event_payments_verification_status')) {
                    $table->index('verification_status', 'idx_event_payments_verification_status');
                }
                if (Schema::hasColumn('event_payments', 'gateway')
                    && !$this->indexExists('event_payments', 'idx_event_payments_gateway')) {
                    $table->index('gateway', 'idx_event_payments_gateway');
                }
                if (Schema::hasColumn('event_payments', 'verified_at')
                    && !$this->indexExists('event_payments', 'idx_event_payments_verified_at')) {
                    $table->index('verified_at', 'idx_event_payments_verified_at');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'approval_status')
                    && !$this->indexExists('users', 'idx_users_approval_status')) {
                    $table->index('approval_status', 'idx_users_approval_status');
                }
                if (Schema::hasColumn('users', 'is_suspended')
                    && !$this->indexExists('users', 'idx_users_is_suspended')) {
                    $table->index('is_suspended', 'idx_users_is_suspended');
                }
                if (Schema::hasColumn('users', 'email_verified_at')
                    && !$this->indexExists('users', 'idx_users_email_verified_at')) {
                    $table->index('email_verified_at', 'idx_users_email_verified_at');
                }
                if (Schema::hasColumn('users', 'last_login_at')
                    && !$this->indexExists('users', 'idx_users_last_login_at')) {
                    $table->index('last_login_at', 'idx_users_last_login_at');
                }
            });
        }

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'user_id') && Schema::hasColumn('bookings', 'status')
                    && !$this->indexExists('bookings', 'idx_bookings_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_bookings_user_status');
                }
                if (Schema::hasColumn('bookings', 'event_id') && Schema::hasColumn('bookings', 'status')
                    && !$this->indexExists('bookings', 'idx_bookings_event_status')) {
                    $table->index(['event_id', 'status'], 'idx_bookings_event_status');
                }
                if (Schema::hasColumn('bookings', 'created_at')
                    && !$this->indexExists('bookings', 'idx_bookings_created_at')) {
                    $table->index('created_at', 'idx_bookings_created_at');
                }
            });
        }

        if (Schema::hasTable('judges')) {
            Schema::table('judges', function (Blueprint $table) {
                if (Schema::hasColumn('judges', 'competition_id') && Schema::hasColumn('judges', 'status')
                    && !$this->indexExists('judges', 'idx_judges_comp_status')) {
                    $table->index(['competition_id', 'status'], 'idx_judges_comp_status');
                }
                if (Schema::hasColumn('judges', 'user_id')
                    && !$this->indexExists('judges', 'idx_judges_user_id')) {
                    $table->index('user_id', 'idx_judges_user_id');
                }
            });
        }

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'user_id') && Schema::hasColumn('notifications', 'read_at')
                    && !$this->indexExists('notifications', 'idx_notifications_user_read')) {
                    $table->index(['user_id', 'read_at'], 'idx_notifications_user_read');
                }
                if (Schema::hasColumn('notifications', 'created_at')
                    && !$this->indexExists('notifications', 'idx_notifications_created_at')) {
                    $table->index('created_at', 'idx_notifications_created_at');
                }
                if (Schema::hasColumn('notifications', 'type')
                    && !$this->indexExists('notifications', 'idx_notifications_type')) {
                    $table->index('type', 'idx_notifications_type');
                }
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'user_id') && Schema::hasColumn('transactions', 'status')
                    && !$this->indexExists('transactions', 'idx_transactions_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_transactions_user_status');
                }
                if (Schema::hasColumn('transactions', 'created_at') && Schema::hasColumn('transactions', 'type')
                    && !$this->indexExists('transactions', 'idx_transactions_created_type')) {
                    $table->index(['created_at', 'type'], 'idx_transactions_created_type');
                }
                if (Schema::hasColumn('transactions', 'reference_id')
                    && !$this->indexExists('transactions', 'idx_transactions_reference_id')) {
                    $table->index('reference_id', 'idx_transactions_reference_id');
                }
            });
        }

        if (Schema::hasTable('mentors')) {
            Schema::table('mentors', function (Blueprint $table) {
                if (Schema::hasColumn('mentors', 'event_id') && Schema::hasColumn('mentors', 'status')
                    && !$this->indexExists('mentors', 'idx_mentors_event_status')) {
                    $table->index(['event_id', 'status'], 'idx_mentors_event_status');
                }
                if (Schema::hasColumn('mentors', 'user_id')
                    && !$this->indexExists('mentors', 'idx_mentors_user_id')) {
                    $table->index('user_id', 'idx_mentors_user_id');
                }
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (Schema::hasColumn('reviews', 'reviewable_type') && Schema::hasColumn('reviews', 'reviewable_id')
                    && !$this->indexExists('reviews', 'idx_reviews_reviewable')) {
                    $table->index(['reviewable_type', 'reviewable_id'], 'idx_reviews_reviewable');
                }
                if (Schema::hasColumn('reviews', 'user_id') && Schema::hasColumn('reviews', 'rating')
                    && !$this->indexExists('reviews', 'idx_reviews_user_rating')) {
                    $table->index(['user_id', 'rating'], 'idx_reviews_user_rating');
                }
                if (Schema::hasColumn('reviews', 'created_at')
                    && !$this->indexExists('reviews', 'idx_reviews_created_at')) {
                    $table->index('created_at', 'idx_reviews_created_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'status') && Schema::hasColumn('events', 'published_at')
                    && $this->indexExists('events', 'idx_events_status_published')) {
                    $table->dropIndex('idx_events_status_published');
                }
                if (Schema::hasColumn('events', 'user_id') && Schema::hasColumn('events', 'status')
                    && $this->indexExists('events', 'idx_events_user_status')) {
                    $table->dropIndex('idx_events_user_status');
                }
                if (Schema::hasColumn('events', 'start_date') && Schema::hasColumn('events', 'end_date')
                    && $this->indexExists('events', 'idx_events_date_range')) {
                    $table->dropIndex('idx_events_date_range');
                }
                if (Schema::hasColumn('events', 'category_id')
                    && $this->indexExists('events', 'idx_events_category_id')) {
                    $table->dropIndex('idx_events_category_id');
                }
            });
        }

        if (Schema::hasTable('event_tickets')) {
            Schema::table('event_tickets', function (Blueprint $table) {
                if (Schema::hasColumn('event_tickets', 'event_id') && Schema::hasColumn('event_tickets', 'ticket_type')
                    && $this->indexExists('event_tickets', 'idx_event_tickets_type')) {
                    $table->dropIndex('idx_event_tickets_type');
                }
            });
        }

        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (Schema::hasColumn('event_registrations', 'user_id') && Schema::hasColumn('event_registrations', 'status')
                    && $this->indexExists('event_registrations', 'idx_event_reg_user_status')) {
                    $table->dropIndex('idx_event_reg_user_status');
                }
                if (Schema::hasColumn('event_registrations', 'ticket_id') && Schema::hasColumn('event_registrations', 'status')
                    && $this->indexExists('event_registrations', 'idx_event_reg_ticket_status')) {
                    $table->dropIndex('idx_event_reg_ticket_status');
                }
                if (Schema::hasColumn('event_registrations', 'created_at') && Schema::hasColumn('event_registrations', 'status')
                    && $this->indexExists('event_registrations', 'idx_event_reg_created_status')) {
                    $table->dropIndex('idx_event_reg_created_status');
                }
            });
        }

        if (Schema::hasTable('event_payments')) {
            Schema::table('event_payments', function (Blueprint $table) {
                if (Schema::hasColumn('event_payments', 'user_id') && Schema::hasColumn('event_payments', 'status')
                    && $this->indexExists('event_payments', 'idx_event_payments_user_status')) {
                    $table->dropIndex('idx_event_payments_user_status');
                }
                if (Schema::hasColumn('event_payments', 'event_id') && Schema::hasColumn('event_payments', 'status')
                    && $this->indexExists('event_payments', 'idx_event_payments_event_status')) {
                    $table->dropIndex('idx_event_payments_event_status');
                }
                if (Schema::hasColumn('event_payments', 'verification_status')
                    && $this->indexExists('event_payments', 'idx_event_payments_verification_status')) {
                    $table->dropIndex('idx_event_payments_verification_status');
                }
                if (Schema::hasColumn('event_payments', 'gateway')
                    && $this->indexExists('event_payments', 'idx_event_payments_gateway')) {
                    $table->dropIndex('idx_event_payments_gateway');
                }
                if (Schema::hasColumn('event_payments', 'verified_at')
                    && $this->indexExists('event_payments', 'idx_event_payments_verified_at')) {
                    $table->dropIndex('idx_event_payments_verified_at');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'approval_status')
                    && $this->indexExists('users', 'idx_users_approval_status')) {
                    $table->dropIndex('idx_users_approval_status');
                }
                if (Schema::hasColumn('users', 'is_suspended')
                    && $this->indexExists('users', 'idx_users_is_suspended')) {
                    $table->dropIndex('idx_users_is_suspended');
                }
                if (Schema::hasColumn('users', 'email_verified_at')
                    && $this->indexExists('users', 'idx_users_email_verified_at')) {
                    $table->dropIndex('idx_users_email_verified_at');
                }
                if (Schema::hasColumn('users', 'last_login_at')
                    && $this->indexExists('users', 'idx_users_last_login_at')) {
                    $table->dropIndex('idx_users_last_login_at');
                }
            });
        }

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'user_id') && Schema::hasColumn('bookings', 'status')
                    && $this->indexExists('bookings', 'idx_bookings_user_status')) {
                    $table->dropIndex('idx_bookings_user_status');
                }
                if (Schema::hasColumn('bookings', 'event_id') && Schema::hasColumn('bookings', 'status')
                    && $this->indexExists('bookings', 'idx_bookings_event_status')) {
                    $table->dropIndex('idx_bookings_event_status');
                }
                if (Schema::hasColumn('bookings', 'created_at')
                    && $this->indexExists('bookings', 'idx_bookings_created_at')) {
                    $table->dropIndex('idx_bookings_created_at');
                }
            });
        }

        if (Schema::hasTable('judges')) {
            Schema::table('judges', function (Blueprint $table) {
                if (Schema::hasColumn('judges', 'competition_id') && Schema::hasColumn('judges', 'status')
                    && $this->indexExists('judges', 'idx_judges_comp_status')) {
                    $table->dropIndex('idx_judges_comp_status');
                }
                if (Schema::hasColumn('judges', 'user_id')
                    && $this->indexExists('judges', 'idx_judges_user_id')) {
                    $table->dropIndex('idx_judges_user_id');
                }
            });
        }

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'user_id') && Schema::hasColumn('notifications', 'read_at')
                    && $this->indexExists('notifications', 'idx_notifications_user_read')) {
                    $table->dropIndex('idx_notifications_user_read');
                }
                if (Schema::hasColumn('notifications', 'created_at')
                    && $this->indexExists('notifications', 'idx_notifications_created_at')) {
                    $table->dropIndex('idx_notifications_created_at');
                }
                if (Schema::hasColumn('notifications', 'type')
                    && $this->indexExists('notifications', 'idx_notifications_type')) {
                    $table->dropIndex('idx_notifications_type');
                }
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'user_id') && Schema::hasColumn('transactions', 'status')
                    && $this->indexExists('transactions', 'idx_transactions_user_status')) {
                    $table->dropIndex('idx_transactions_user_status');
                }
                if (Schema::hasColumn('transactions', 'created_at') && Schema::hasColumn('transactions', 'type')
                    && $this->indexExists('transactions', 'idx_transactions_created_type')) {
                    $table->dropIndex('idx_transactions_created_type');
                }
                if (Schema::hasColumn('transactions', 'reference_id')
                    && $this->indexExists('transactions', 'idx_transactions_reference_id')) {
                    $table->dropIndex('idx_transactions_reference_id');
                }
            });
        }

        if (Schema::hasTable('mentors')) {
            Schema::table('mentors', function (Blueprint $table) {
                if (Schema::hasColumn('mentors', 'event_id') && Schema::hasColumn('mentors', 'status')
                    && $this->indexExists('mentors', 'idx_mentors_event_status')) {
                    $table->dropIndex('idx_mentors_event_status');
                }
                if (Schema::hasColumn('mentors', 'user_id')
                    && $this->indexExists('mentors', 'idx_mentors_user_id')) {
                    $table->dropIndex('idx_mentors_user_id');
                }
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (Schema::hasColumn('reviews', 'reviewable_type') && Schema::hasColumn('reviews', 'reviewable_id')
                    && $this->indexExists('reviews', 'idx_reviews_reviewable')) {
                    $table->dropIndex('idx_reviews_reviewable');
                }
                if (Schema::hasColumn('reviews', 'user_id') && Schema::hasColumn('reviews', 'rating')
                    && $this->indexExists('reviews', 'idx_reviews_user_rating')) {
                    $table->dropIndex('idx_reviews_user_rating');
                }
                if (Schema::hasColumn('reviews', 'created_at')
                    && $this->indexExists('reviews', 'idx_reviews_created_at')) {
                    $table->dropIndex('idx_reviews_created_at');
                }
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $schema = DB::getDatabaseName();
        $result = DB::selectOne(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$schema, $table, $index]
        );

        return $result !== null;
    }
};
