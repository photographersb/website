<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$event = App\Models\Event::where('status', 'published')->first();
if (!$event) {
    echo json_encode(['error' => 'no_event']);
    exit;
}

$ticket = App\Models\EventTicket::where('event_id', $event->id)
    ->where('is_active', 1)
    ->first();
if (!$ticket) {
    echo json_encode(['error' => 'no_ticket']);
    exit;
}

$email = 'approved_' . Illuminate\Support\Str::random(8) . '@example.test';
$approved = App\Models\User::create([
    'name' => 'Approved Tester',
    'email' => $email,
    'password' => 'Password123!',
    'role' => 'client',
    'approval_status' => 'approved',
    'email_verified_at' => now(),
]);

$pending = App\Models\User::where('approval_status', 'pending')->first();
if (!$pending) {
    $pending = App\Models\User::where('id', '<>', $approved->id)->first();
    if ($pending) {
        $pending->update(['approval_status' => 'pending']);
    }
}

$approvedToken = $approved->createToken('http-test-approved')->plainTextToken;
$pendingToken = $pending ? $pending->createToken('http-test-pending')->plainTextToken : null;

$result = [
    'event_id' => $event->id,
    'ticket_id' => $ticket->id,
    'approved_token' => $approvedToken,
    'pending_token' => $pendingToken,
    'approved_user_id' => $approved->id,
    'pending_user_id' => $pending ? $pending->id : null,
];

echo json_encode($result);
