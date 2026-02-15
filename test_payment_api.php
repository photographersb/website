<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use App\Models\Event;
use App\Models\EventTicket;
use App\Http\Controllers\Api\EventPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Simulate request for user #7
$user = User::find(7);
Auth::login($user);

echo "==========================================\n";
echo "Simulating Manual Payment Request\n";
echo "==========================================\n\n";
echo "User: {$user->name} (ID: {$user->id})\n";
echo "Event: 1 (Iftar bazar)\n";
echo "Ticket: 1 (General Admission)\n\n";

$event = Event::findOrFail(1);
$ticket = EventTicket::findOrFail(1);

echo "Ticket Price: ৳{$ticket->price}\n";
echo "Available: {$ticket->available_quantity}\n\n";

// Create request
$request = Request::create('/api/v1/events/1/payments/manual', 'POST', [
    'ticket_id' => '1',
    'qty' => '1',
    'method' => 'manual',
    'sender_number' => '01712345678',
]);

$controller = new EventPaymentController();

try {
    $response = $controller->manual($request, $event);
    $content = json_decode($response->getContent(), true);
    
    echo "==========================================\n";
    echo "API Response:\n";
    echo "==========================================\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response Body:\n";
    echo json_encode($content, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($content['status'] === 'error') {
        echo "❌ ERROR: {$content['message']}\n";
    } else {
        echo "✅ SUCCESS: {$content['message']}\n";
    }
} catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
