<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\Photographer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PaymentSmokeTest extends Command
{
    protected $signature = 'sb:payment-smoke
        {--cleanup : Remove prior payment smoke test data}
        {--event-id= : Use an existing event ID}
        {--ticket-id= : Use an existing ticket ID}';

    protected $description = 'Create smoke-test users/tokens and an event ticket for payment endpoint testing';

    public function handle(): int
    {
        if ($this->option('cleanup')) {
            return $this->cleanup();
        }

        $event = null;
        $ticket = null;

        $eventId = $this->option('event-id');
        $ticketId = $this->option('ticket-id');

        if ($ticketId) {
            $ticket = EventTicket::find($ticketId);
            if (!$ticket) {
                $this->error("Ticket {$ticketId} not found.");
                return self::FAILURE;
            }
            $event = $ticket->event;
        }

        if ($eventId) {
            $event = Event::find($eventId);
            if (!$event) {
                $this->error("Event {$eventId} not found.");
                return self::FAILURE;
            }
            if ($ticket && $ticket->event_id !== $event->id) {
                $this->error('Ticket does not belong to the provided event.');
                return self::FAILURE;
            }
        }

        if (!$event) {
            $event = $this->createEventWithTicket();
            if (!$event) {
                return self::FAILURE;
            }
            $ticket = $event->tickets()->first();
        }

        if (!$ticket) {
            $ticket = $this->createTicket($event);
        }

        $approvedUser = $this->createUser('approved');
        $pendingUser = $this->createUser('pending');

        if (!$approvedUser || !$pendingUser) {
            $this->error('Failed to create test users.');
            return self::FAILURE;
        }

        $approvedToken = $approvedUser->createToken('payment_smoke')->plainTextToken;
        $pendingToken = $pendingUser->createToken('payment_smoke')->plainTextToken;

        $this->info('Payment smoke test setup complete.');
        $this->line('');
        $this->line("Event ID: {$event->id}");
        $this->line("Ticket ID: {$ticket->id}");
        $this->line('');
        $this->line("Approved user: {$approvedUser->email}");
        $this->line("Approved token: {$approvedToken}");
        $this->line('');
        $this->line("Pending user: {$pendingUser->email}");
        $this->line("Pending token: {$pendingToken}");

        return self::SUCCESS;
    }

    private function createEventWithTicket(): ?Event
    {
        $stamp = now()->format('YmdHis');

        return DB::transaction(function () use ($stamp) {
            $organizerUser = $this->createOrganizerUser($stamp);
            if (!$organizerUser) {
                return null;
            }

            $photographer = Photographer::create([
                'user_id' => $organizerUser->id,
                'slug' => 'payment-smoke-organizer-' . $stamp,
                'bio' => 'Payment smoke test organizer',
                'experience_years' => 1,
            ]);

            $event = Event::create([
                'organizer_id' => $photographer->id,
                'title' => 'Payment Smoke Test Event ' . $stamp,
                'slug' => 'payment-smoke-' . $stamp,
                'description' => 'Temporary event for payment smoke testing.',
                'event_date' => now()->addDays(7),
                'event_end_date' => now()->addDays(7)->addHours(2),
                'location' => 'Dhaka',
                'status' => 'published',
                'is_ticketed' => true,
                'ticket_price' => 500,
                'require_registration' => true,
            ]);

            $this->createTicket($event);

            return $event;
        });
    }

    private function createTicket(Event $event): EventTicket
    {
        return EventTicket::create([
            'event_id' => $event->id,
            'title' => 'General Admission',
            'price' => 500,
            'quantity' => 10,
            'sold_count' => 0,
            'sales_start_datetime' => now()->subDay(),
            'sales_end_datetime' => now()->addDays(7),
            'is_active' => true,
        ]);
    }

    private function createOrganizerUser(string $stamp): ?User
    {
        $user = User::create([
            'name' => 'Payment Smoke Organizer',
            'email' => "payment_smoke_organizer_{$stamp}@example.test",
            'password' => 'Password123!',
            'role' => 'photographer',
            'email_verified_at' => now(),
        ]);

        $this->applyApproval($user, true);

        return $user;
    }

    private function createUser(string $status): ?User
    {
        $stamp = now()->format('YmdHis') . '-' . Str::random(4);
        $user = User::create([
            'name' => ucfirst($status) . ' Payment Tester',
            'email' => "payment_smoke_{$status}_{$stamp}@example.test",
            'password' => 'Password123!',
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        $this->applyApproval($user, $status === 'approved');

        return $user;
    }

    private function applyApproval(User $user, bool $approved): void
    {
        if (!Schema::hasColumn('users', 'approval_status')) {
            return;
        }

        $user->forceFill([
            'approval_status' => $approved ? 'approved' : 'pending',
            'approved_at' => $approved ? now() : null,
        ])->save();
    }

    private function cleanup(): int
    {
        $emailPrefix = 'payment_smoke_%@example.test';
        $eventSlugPrefix = 'payment-smoke-%';

        $userIds = User::where('email', 'like', $emailPrefix)->pluck('id')->all();
        if (!empty($userIds)) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', User::class)
                ->whereIn('tokenable_id', $userIds)
                ->delete();

            User::whereIn('id', $userIds)->delete();
        }

        Event::where('slug', 'like', $eventSlugPrefix)->delete();

        $this->info('Payment smoke test data cleaned up.');
        $this->line('');
        $this->line('Deleted users: ' . count($userIds));

        return self::SUCCESS;
    }
}
