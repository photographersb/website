<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('event_tickets')) {
            return;
        }

        $hasBasePrice = Schema::hasColumn('events', 'base_price');

        $eventsQuery = DB::table('events')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('event_mode', 'paid')
                    ->orWhere('is_ticketed', 1)
                    ->orWhere('ticket_price', '>', 0)
                    ->orWhere('price', '>', 0);
            });

        if ($hasBasePrice) {
            $eventsQuery->orWhere('base_price', '>', 0);
        }

        $events = $eventsQuery->get();

        foreach ($events as $event) {
            $hasTickets = DB::table('event_tickets')
                ->where('event_id', $event->id)
                ->exists();

            if ($hasTickets) {
                continue;
            }

            $basePrice = $hasBasePrice ? ($event->base_price ?? null) : null;
            $price = $event->ticket_price ?? $event->price ?? $basePrice ?? 0;
            if (!$price || $price <= 0) {
                continue;
            }

            $quantity = $event->max_attendees ?? $event->capacity ?? 100;
            $salesStart = Carbon::now();
            $salesEnd = $event->registration_deadline ?? $event->event_date ?? Carbon::now()->addDays(30);
            $salesEnd = Carbon::parse($salesEnd);

            if ($salesEnd->lessThan($salesStart)) {
                $salesEnd = $salesStart->copy()->addDays(1);
            }

            DB::table('event_tickets')->insert([
                'event_id' => $event->id,
                'title' => 'General Admission',
                'price' => $price,
                'quantity' => $quantity,
                'sold_count' => 0,
                'sales_start_datetime' => $salesStart,
                'sales_end_datetime' => $salesEnd,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('event_tickets')
            ->where('title', 'General Admission')
            ->delete();
    }
};
