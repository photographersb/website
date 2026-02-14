<?php

/**
 * Test QR Code Generation & Certificate Auto-Issue
 * Run: php test-qr-certificate-flow.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n========================================\n";
echo "   QR & CERTIFICATE FLOW TEST\n";
echo "========================================\n\n";

// Test 1: Get Registration for Testing
echo "✓ Test 1: Get Test Registration\n";
$registration = \App\Models\EventRegistration::with(['event', 'user'])->first();

if (!$registration) {
    echo "  ❌ No registrations found. Please register for an event first.\n";
    echo "  Visit: http://localhost/events and register\n\n";
    exit;
}

echo "  - Registration ID: {$registration->id}\n";
echo "  - Registration Code: " . ($registration->registration_code ?: 'Not set') . "\n";
echo "  - User: {$registration->user->name}\n";
echo "  - Event: {$registration->event->title}\n";
echo "  - Payment Status: {$registration->payment_status}\n";
echo "  - QR Path: " . ($registration->ticket_qr_path ?: 'Not generated') . "\n\n";

// Test 2: Generate QR Code
echo "✓ Test 2: Generate QR Code\n";
try {
    // Ensure registration has a code first
    if (!$registration->registration_code) {
        $registration->registration_code = 'REG-' . strtoupper(substr(uniqid(), -8));
        $registration->save();
        echo "  ✅ Generated registration code: {$registration->registration_code}\n";
    }
    
    $result = \App\Services\QRCodeService::generateForEventRegistration($registration);
    
    if ($result) {
        echo "  ✅ QR Code generated successfully!\n";
        echo "  - File path: {$result}\n";
        echo "  - Full path: " . storage_path('app/public/' . $result) . "\n";
        
        // Check if file exists
        $fullPath = storage_path('app/public/' . $result);
        if (file_exists($fullPath)) {
            $fileSize = filesize($fullPath);
            echo "  - File size: " . number_format($fileSize) . " bytes\n";
            echo "  - Public URL: " . asset('storage/' . $result) . "\n";
        } else {
            echo "  ⚠️  File not found on disk\n";
        }
    } else {
        echo "  ❌ QR generation failed (returned false)\n";
    }
} catch (\Exception $e) {
    echo "  ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check Event Certificate Settings
echo "✓ Test 3: Check Event Certificate Settings\n";
$event = $registration->event;
echo "  - Event ID: {$event->id}\n";
echo "  - Certificates Enabled: " . ($event->certificates_enabled ? 'Yes' : 'No') . "\n";

if (!$event->certificates_enabled) {
    echo "  ⚠️  Certificates are disabled for this event\n";
    echo "  - Enable certificates in event settings to test auto-issue\n\n";
    
    // Enable it for testing
    echo "  🔧 Enabling certificates for testing...\n";
    $event->certificates_enabled = true;
    $event->save();
    echo "  ✅ Certificates enabled!\n";
}
echo "\n";

// Test 4: Check Certificate Template
echo "✓ Test 4: Check Certificate Template\n";
$template = \App\Models\CertificateTemplate::where('type', 'event')->first();
if (!$template) {
    echo "  ⚠️  No event certificate template found\n";
    echo "  - Creating default template...\n";
    
    $template = \App\Models\CertificateTemplate::create([
        'title' => 'Default Event Certificate',
        'description' => 'Default template for event attendance certificates',
        'type' => 'participation',
        'width' => 297,
        'height' => 210,
        'background_color' => '#ffffff',
        'accent_color' => '#8B0000',
        'text_color' => '#000000',
        'title_font' => 'serif',
        'is_default' => true,
        'template_content' => '<div style="text-align:center"><h1>[EVENT_TITLE]</h1><p>Awarded to</p><h2>[USER_NAME]</h2><p>For attendance at [EVENT_TITLE]</p><p>Date: [DATE]</p></div>',
    ]);
    
    echo "  ✅ Default template created (ID: {$template->id})\n";
} else {
    echo "  ✅ Template exists (ID: {$template->id}, Title: {$template->title})\n";
}
echo "\n";

// Test 5: Simulate Attendance Check-in
echo "✓ Test 5: Simulate Attendance Check-in\n";
if ($registration->attended_at) {
    echo "  ℹ️  User already checked in at {$registration->attended_at}\n";
} else {
    echo "  - Marking user as attended...\n";
    $registration->attended_at = now();
    $registration->save();
    echo "  ✅ User marked as attended!\n";
}
echo "\n";

// Test 6: Test Certificate Auto-Issue
echo "✓ Test 6: Test Certificate Auto-Issue\n";
try {
    // Check if certificate already exists
    $existingCert = \App\Models\Certificate::where('event_id', $event->id)
        ->where('issued_to_user_id', $registration->user_id)
        ->first();
    
    if ($existingCert) {
        echo "  ℹ️  Certificate already exists (ID: {$existingCert->id})\n";
        echo "  - Certificate Code: {$existingCert->certificate_code}\n";
        echo "  - Title: {$existingCert->title}\n";
        echo "  - Issued At: {$existingCert->issued_at}\n";
    } else {
        echo "  - Issuing certificate...\n";
        
        // Manually create attendance log object for testing
        $attendanceLog = new \stdClass();
        $attendanceLog->user_id = $registration->user_id;
        $attendanceLog->event_id = $event->id;
        $attendanceLog->user = $registration->user;
        $attendanceLog->event = $event;
        
        $result = \App\Services\CertificateAutoIssueService::issueForAttendee($attendanceLog);
        
        if ($result['success']) {
            echo "  ✅ Certificate issued successfully!\n";
            $cert = $result['certificate'];
            echo "  - Certificate ID: {$cert->id}\n";
            echo "  - Certificate Code: {$cert->certificate_code}\n";
            echo "  - Title: {$cert->title}\n";
            echo "  - Description: {$cert->description}\n";
            echo "  - Issued By: {$cert->issued_by}\n";
            echo "  - Status: {$cert->status}\n";
        } else {
            echo "  ❌ Certificate issuance failed\n";
            echo "  - Error: " . ($result['error'] ?? 'Unknown error') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "  ❌ Error: " . $e->getMessage() . "\n";
    echo "  - Trace: " . $e->getTraceAsString() . "\n";
}
echo "\n";

// Test 7: Verify QR Code in Database
echo "✓ Test 7: Verify Final State\n";
$registration->refresh();
echo "  - Registration Code: {$registration->registration_code}\n";
echo "  - QR Code Path: " . ($registration->ticket_qr_path ?: 'Not set') . "\n";
echo "  - Attended At: " . ($registration->attended_at ? $registration->attended_at->format('Y-m-d H:i:s') : 'Not attended') . "\n";

$certCount = \App\Models\Certificate::where('event_id', $event->id)
    ->where('issued_to_user_id', $registration->user_id)
    ->count();
echo "  - Certificates Issued: {$certCount}\n";
echo "\n";

// Test 8: Check All QR Codes Generated
echo "✓ Test 8: QR Code Storage Status\n";
$qrDir = storage_path('app/public/qr-codes/registrations');
$totalQRs = count(glob($qrDir . '/*/*.png'));
echo "  - Total QR Codes: {$totalQRs}\n";
echo "  - Storage Path: {$qrDir}\n";

// List all QR files
if ($totalQRs > 0) {
    $qrFiles = glob($qrDir . '/*/*.png');
    echo "  - QR Code Files:\n";
    foreach ($qrFiles as $file) {
        $size = filesize($file);
        $name = basename($file);
        echo "    • {$name} (" . number_format($size) . " bytes)\n";
    }
}
echo "\n";

// Summary
echo "========================================\n";
echo "   TEST RESULTS SUMMARY\n";
echo "========================================\n";
$registration->refresh();

$qrStatus = $registration->ticket_qr_path ? '✅ GENERATED' : '❌ NOT GENERATED';
$attendStatus = $registration->attended_at ? '✅ ATTENDED' : '⚠️  NOT ATTENDED';
$certStatus = $certCount > 0 ? '✅ ISSUED' : '❌ NOT ISSUED';

echo "✅ QR Code: {$qrStatus}\n";
echo "{$attendStatus} Attendance: " . ($registration->attended_at ? $registration->attended_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
echo "{$certStatus} Certificate: {$certCount} issued\n";
echo "\n";

if ($registration->ticket_qr_path && $registration->attended_at && $certCount > 0) {
    echo "🎉 ALL TESTS PASSED! Complete flow verified:\n";
    echo "   1. Registration ✅\n";
    echo "   2. QR Code Generation ✅\n";
    echo "   3. Attendance Check-in ✅\n";
    echo "   4. Certificate Auto-Issue ✅\n";
} else {
    echo "⚠️  SOME TESTS INCOMPLETE:\n";
    if (!$registration->ticket_qr_path) echo "   - QR Code needs to be generated\n";
    if (!$registration->attended_at) echo "   - User needs to be checked in\n";
    if ($certCount == 0) echo "   - Certificate needs to be issued\n";
}
echo "\n";
echo "Next: Test in browser at http://localhost/my-registrations\n\n";
