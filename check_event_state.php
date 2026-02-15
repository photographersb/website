<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\EventRegistration;
use App\Models\EventPayment;
use App\Models\User;

$event = Event::where('slug', 'iftar-bazar')->first();
if (!$event) {
    die("Event not found\n");
}

echo "Event: {$event->title} (ID: {$event->id})\n";
echo "Status: {$event->status}\n";
echo "Registration Deadline: " . ($event->registration_deadline ?? 'None') . "\n\n";

echo "Tickets:\n";
$tickets = $event->tickets;
foreach ($tickets as $ticket) {
    echo "  ID: {$ticket->id} | {$ticket->title}\n";
    echo "    Active: " . ($ticket->is_active ? 'Yes' : 'No') . "\n";
    echo "    On Sale: " . ($ticket->isOnSale() ? 'Yes' : 'No') . "\n";
    echo "    Available: {$ticket->getAvailableQuantity()}\n";
    echo "    Sales Start: {$ticket->sales_start_date}\n";
    echo "    Sales End: {$ticket->sales_end_date}\n\n";
}

echo "\nPending Registrations:\n";
$pendingRegs = EventRegistration::where('event_id', $event->id)
    ->where('status', 'pending_payment')
    ->get();

foreach ($pendingRegs as $reg) {
    $user = User::find($reg->user_id);
    echo "  User: {$user->name} (ID: {$reg->user_id})\n";
    echo "  Ticket ID: {$reg->ticket_id}\n";
    echo "  Qty: {$reg->qty}\n";
    echo "  Created: {$reg->created_at}\n";
    echo "  Age: " . now()->diffInMinutes($reg->created_at) . " minutes\n\n";
}

echo "Total pending registrations: " . $pendingRegs->count() . "\n";
