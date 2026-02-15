<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;
use App\Models\EventPayment;

// User ID 7 trying to register for event ID 1
$userId = 7;
$eventId = 1;

echo "==========================================\n";
echo "Checking pending payments for User #$userId\n";
echo "==========================================\n\n";

$registrations = EventRegistration::where('event_id', $eventId)
    ->where('user_id', $userId)
    ->with('payment')
    ->orderBy('created_at', 'desc')
    ->get();

if ($registrations->isEmpty()) {
    echo "✅ NO registrations found\n";
} else {
    echo "Found {$registrations->count()} registration(s):\n\n";
    foreach ($registrations as $reg) {
        echo "Registration #{$reg->id}\n";
        echo "  Status: {$reg->status}\n";
        echo "  Ticket ID: {$reg->ticket_id}\n";
        echo "  Quantity: {$reg->qty}\n";
        echo "  Created: {$reg->created_at}\n";
        
        $ageMinutes = now()->diffInMinutes($reg->created_at);
        echo "  Age: {$ageMinutes} minutes\n";
        
        if ($reg->status === 'pending_payment') {
            if ($ageMinutes > 60) {
                echo "  ⚠️  OLD pending payment (>60 min) - should be cleaned up\n";
            } else {
                echo "  🚫 ACTIVE pending payment (<60 min) - BLOCKING new submissions\n";
            }
        }
        
        if ($reg->payment) {
            echo "  Payment ID: {$reg->payment->id}\n";
            echo "  Payment Status: {$reg->payment->status}\n";
            echo "  Payment Method: {$reg->payment->method}\n";
        }
        echo "\n";
    }
}

// Also check standalone payments
$payments = EventPayment::where('event_id', $eventId)
    ->where('user_id', $userId)
    ->orderBy('created_at', 'desc')
    ->get();

echo "\nFound {$payments->count()} payment record(s):\n";
foreach ($payments as $payment) {
    echo "  Payment #{$payment->id} - Status: {$payment->status}, Method: {$payment->method}, Created: {$payment->created_at}\n";
}
