# QR Code Implementation Guide for Event Tickets

## Overview
Event registrations automatically generate a unique QR token (32-character random string). This guide explains how to generate and display scannable QR codes.

## Step 1: Install QR Code Library

```bash
composer require chillerlan/php-qrcode
```

This provides the `QRCode` class for generating QR codes.

## Step 2: Create QR Code Service

Create `app/Services/QrCodeService.php`:

```php
<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate QR code for event registration
     */
    public static function generateForRegistration($qrToken, $eventId = null): string
    {
        $data = $eventId 
            ? "event:{$eventId}|token:{$qrToken}" 
            : "token:{$qrToken}";

        $qrCode = new QRCode();
        $qrCode->setErrorCorrectLevel(QRCode::ECC_L);
        $qrCode->addData($data);
        $qrCode->make();

        return $qrCode->render();
    }

    /**
     * Generate and save QR code to storage
     */
    public static function saveForRegistration($qrToken, $eventId): string
    {
        $data = "event:{$eventId}|token:{$qrToken}";
        $filename = "qr-codes/{$eventId}-{$qrToken}.svg";

        $qrCode = new QRCode();
        $qrCode->addData($data);
        $qrCode->make();

        Storage::disk('public')->put($filename, $qrCode->render());

        return Storage::disk('public')->url($filename);
    }

    /**
     * Generate QR code as PNG
     */
    public static function generatePng($qrToken, $eventId = null): string
    {
        $data = $eventId 
            ? "event:{$eventId}|token:{$qrToken}" 
            : "token:{$qrToken}";

        $qrCode = new QRCode([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => false,
        ]);
        $qrCode->addData($data);
        $qrCode->make();

        return $qrCode->render();
    }

    /**
     * Generate QR code as base64 (for embedding in HTML/emails)
     */
    public static function generateBase64($qrToken, $eventId = null): string
    {
        $data = $eventId 
            ? "event:{$eventId}|token:{$qrToken}" 
            : "token:{$qrToken}";

        $qrCode = new QRCode([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => true,
        ]);
        $qrCode->addData($data);
        $qrCode->make();

        return $qrCode->render();
    }
}
```

## Step 3: Update EventRegistration Model

Add accessor to get QR code URL:

```php
// In App\Models\EventRegistration

use App\Services\QrCodeService;

public function getQrCodeAttribute(): string
{
    return QrCodeService::generateBase64($this->qr_token, $this->event_id);
}

public function getQrCodeUrlAttribute(): string
{
    return route('registration.qr-code', ['registration' => $this->id]);
}
```

## Step 4: Create QR Code Route

Add to `routes/api.php`:

```php
// Public QR code download
Route::get('/registrations/{registration}/qr-code', function (\App\Models\EventRegistration $registration) {
    return response($registration->qr_code_image)
        ->header('Content-Type', 'image/png')
        ->header('Content-Disposition', "inline; filename={$registration->event->slug}-{$registration->id}.png");
})->name('registration.qr-code');

// PDF ticket with QR code
Route::get('/registrations/{registration}/ticket', [\App\Http\Controllers\Api\TicketController::class, 'download'])->name('registration.ticket');
```

## Step 5: Scanner Implementation (Frontend)

### Vue Component for Scanner

Create `resources/js/components/EventCheckInScanner.vue`:

```vue
<template>
  <div class="check-in-scanner">
    <div v-if="!cameraActive" class="camera-controls">
      <button @click="startCamera" class="btn btn-primary">
        Start Camera
      </button>
    </div>

    <div v-else class="scanner-container">
      <video ref="video" class="scanner-video" autoplay></video>
      <canvas ref="canvas" class="hidden"></canvas>

      <div class="scanner-controls">
        <button @click="stopCamera" class="btn btn-secondary">
          Stop Camera
        </button>
      </div>

      <div v-if="scanResult" class="scan-result" :class="scanResult.success ? 'success' : 'error'">
        <p>{{ scanResult.message }}</p>
        <p v-if="scanResult.details">{{ scanResult.details }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { BrowserQRCodeReader } from '@zxing/browser';

export default {
  name: 'EventCheckInScanner',
  props: {
    eventId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      cameraActive: false,
      codeReader: null,
      scanResult: null,
    };
  },
  mounted() {
    this.codeReader = new BrowserQRCodeReader();
  },
  methods: {
    async startCamera() {
      try {
        this.cameraActive = true;
        const result = await this.codeReader.decodeFromVideoElement(
          this.$refs.video,
          async (result) => {
            if (result) {
              await this.processQrCode(result.text);
            }
          }
        );
      } catch (err) {
        this.scanResult = {
          success: false,
          message: 'Camera error: ' + err.message,
        };
      }
    },

    async stopCamera() {
      this.cameraActive = false;
      if (this.codeReader) {
        await this.codeReader.reset();
      }
    },

    async processQrCode(qrData) {
      try {
        // Extract token from QR data
        const tokenMatch = qrData.match(/token:([a-zA-Z0-9]+)/);
        if (!tokenMatch) {
          this.scanResult = {
            success: false,
            message: 'Invalid QR code format',
          };
          return;
        }

        const token = tokenMatch[1];

        // Send to backend
        const response = await fetch(
          `/api/v1/admin/events/${this.eventId}/check-in/scan`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${this.$store.state.auth.token}`,
            },
            body: JSON.stringify({ qr_token: token }),
          }
        );

        const data = await response.json();

        this.scanResult = {
          success: data.success,
          message: data.message,
          details: data.registration
            ? `${data.registration.user.name} - ${data.registration.ticket?.title || 'N/A'}`
            : '',
        };

        // Auto-clear success messages after 3 seconds
        if (data.success) {
          setTimeout(() => {
            this.scanResult = null;
          }, 3000);
        }
      } catch (error) {
        this.scanResult = {
          success: false,
          message: 'Error: ' + error.message,
        };
      }
    },
  },
};
</script>

<style scoped>
.check-in-scanner {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
}

.scanner-container {
  position: relative;
}

.scanner-video {
  width: 100%;
  border: 2px solid #ccc;
  border-radius: 8px;
}

.scanner-controls {
  margin-top: 1rem;
  text-align: center;
}

.scan-result {
  margin-top: 1rem;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
}

.scan-result.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.scan-result.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.hidden {
  display: none;
}
</style>
```

## Step 6: Display QR Code in Ticket

Create `resources/js/components/UserEventTicket.vue`:

```vue
<template>
  <div class="ticket-card">
    <div class="ticket-header">
      <h3>{{ registration.event.title }}</h3>
      <span class="ticket-type">{{ registration.ticket.title }}</span>
    </div>

    <div class="ticket-details">
      <p><strong>Date:</strong> {{ formatDate(registration.event.start_datetime) }}</p>
      <p><strong>Venue:</strong> {{ registration.event.venue }}</p>
      <p><strong>Status:</strong> <span :class="`badge badge-${statusClass}`">{{ registration.status }}</span></p>
    </div>

    <div class="ticket-qr">
      <p>Scan this code for entry:</p>
      <img :src="`data:image/png;base64,${registration.qr_code}`" alt="Ticket QR Code" class="qr-image">
      <p class="qr-token">{{ registration.qr_token }}</p>
    </div>

    <div class="ticket-actions">
      <button @click="downloadTicket" class="btn btn-primary">
        Download PDF
      </button>
      <button @click="shareTicket" class="btn btn-secondary">
        Share
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserEventTicket',
  props: {
    registration: {
      type: Object,
      required: true,
    },
  },
  computed: {
    statusClass() {
      const statusMap = {
        confirmed: 'success',
        pending_payment: 'warning',
        attended: 'info',
        cancelled: 'danger',
        refunded: 'secondary',
      };
      return statusMap[this.registration.status] || 'secondary';
    },
  },
  methods: {
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },

    async downloadTicket() {
      window.open(
        `/api/v1/registrations/${this.registration.id}/ticket`,
        '_blank'
      );
    },

    async shareTicket() {
      const text = `I'm attending ${this.registration.event.title} on ${this.formatDate(this.registration.event.start_datetime)}`;
      
      if (navigator.share) {
        navigator.share({
          title: this.registration.event.title,
          text: text,
          url: window.location.href,
        });
      } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(text);
        alert('Event details copied to clipboard');
      }
    },
  },
};
</script>

<style scoped>
.ticket-card {
  border: 2px solid #007bff;
  border-radius: 8px;
  padding: 1.5rem;
  background: white;
  margin-bottom: 1rem;
}

.ticket-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}

.ticket-type {
  background-color: #007bff;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
}

.ticket-details {
  margin-bottom: 1.5rem;
}

.ticket-details p {
  margin: 0.5rem 0;
}

.ticket-qr {
  text-align: center;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.qr-image {
  max-width: 200px;
  margin: 0.5rem 0;
  border: 1px solid #ccc;
  padding: 0.5rem;
}

.qr-token {
  font-family: monospace;
  font-size: 0.875rem;
  color: #666;
  word-break: break-all;
}

.ticket-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
}

.badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.875rem;
}

.badge-success { background-color: #28a745; color: white; }
.badge-warning { background-color: #ffc107; color: black; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-danger { background-color: #dc3545; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
</style>
```

## Step 7: Configure Storage

Ensure QR codes can be stored in public directory:

```bash
php artisan storage:link
```

Update `config/filesystems.php` if needed:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'path' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

## Step 8: Testing QR Codes

```php
// Test QR code generation
use App\Services\QrCodeService;

// Generate as SVG (HTML)
$svg = QrCodeService::generateForRegistration('abc123def456', 1);

// Generate as base64 PNG
$base64 = QrCodeService::generateBase64('abc123def456', 1);

// Save and get URL
$url = QrCodeService::saveForRegistration('abc123def456', 1);
```

## Step 9: NPM Dependencies for Scanner

Install frontend QR scanner:

```bash
npm install @zxing/browser @zxing/library
```

## QR Code Format

The QR code encodes the following data:
```
event:1|token:abc123def456xyz789
```

Scanner reads this and extracts:
- `event`: Event ID
- `token`: Registration QR token (32-char unique string)

## Performance Optimization

For high-volume scanning:

1. Cache QR code images:
```php
// Generate once and cache
Cache::remember("qr-{$registration->id}", 3600, function () {
    return QrCodeService::generateBase64($registration->qr_token, $registration->event_id);
});
```

2. Use WebP format for smaller file sizes:
```php
$qrCode->setImageType('webp');
```

3. Pre-generate QR codes in background job:
```php
Queue::job(new GenerateQrCodesJob($event));
```

## Troubleshooting

**QR Code Not Scanning**
- Verify error correction level is set correctly
- Ensure QR code is large enough (minimum 200x200px)
- Check lighting conditions for camera scanner

**Performance Issues**
- Pre-generate and cache QR codes
- Use CDN for QR image storage
- Implement lazy loading for ticket pages

**Display Issues**
- Ensure MIME types are correct
- Check browser compatibility for data URLs
- Verify base64 encoding is complete

## Production Deployment

1. Enable storage symlink
2. Configure CDN for QR images
3. Set up image caching headers
4. Monitor QR generation performance
5. Implement fallback manual entry

