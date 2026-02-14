# ⚡ Social Login & Refunds - Quick Reference

## 🔐 Social Login API Endpoints

### 1. Get OAuth Redirect URL
```bash
GET /api/v1/auth/{provider}/redirect
# provider: google | facebook | apple
```

**Response:**
```json
{
  "redirect_url": "https://accounts.google.com/o/oauth2/auth?client_id=..."
}
```

### 2. Handle OAuth Callback  
```bash
GET /api/v1/auth/{provider}/callback?code=xxx&state=xxx
```

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": "2024-01-15T10:30:00Z"
  },
  "token": "1|abc123..."
}
```

### 3. Link Account (Protected)
```bash
POST /api/v1/auth/link-social-account
Authorization: Bearer {token}

Body:
{
  "provider": "google",
  "provider_id": "123456789",
  "provider_email": "john@gmail.com",
  "token": "oauth_access_token"
}
```

### 4. Unlink Account (Protected)
```bash
POST /api/v1/auth/unlink-social-account
Authorization: Bearer {token}

Body:
{
  "provider": "google"
}
```

### 5. Get Linked Accounts (Protected)
```bash
GET /api/v1/auth/linked-accounts
Authorization: Bearer {token}
```

---

## 💰 Refund Processing

### PHP Usage
```php
use App\Services\PaymentService;

$paymentService = app(PaymentService::class);

$result = $paymentService->processRefund(
    transaction: $transaction,
    amount: 1500.00,
    reason: 'Customer cancellation'
);

// Returns:
[
    'status' => 'completed',
    'refund_reference' => 'REF1234567890',
    'message' => 'Refund processed successfully',
    'gateway_response' => [...]
]
```

### Supported Gateways
- **SSLCommerz**: Instant refund
- **bKash**: Instant refund  
- **Nagad**: Instant refund
- **Bank Transfer**: Pending (manual)

---

## 🎨 Vue UI Components

### Toast
```vue
<Toast 
  type="success"
  message="Payment completed!"
  :duration="5000"
/>
```

### Alert
```vue
<Alert 
  type="error"
  title="Error"
  message="Upload failed"
  :dismissible="true"
/>
```

### Form Error
```vue
<FormError :error="errors.email" />
```

### Skeleton
```vue
<Skeleton type="card" />
```

---

## 🔧 .env Configuration

```env
GOOGLE_CLIENT_ID=xxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxx
GOOGLE_REDIRECT_URI=${APP_URL}/api/v1/auth/google/callback

FACEBOOK_CLIENT_ID=xxx
FACEBOOK_CLIENT_SECRET=xxx
FACEBOOK_REDIRECT_URI=${APP_URL}/api/v1/auth/facebook/callback
```

---

## 🚀 Quick Deploy

```bash
# 1. Run migration
php artisan migrate

# 2. Clear cache
php artisan config:cache
php artisan route:cache

# 3. Test OAuth
curl http://localhost/api/v1/auth/google/redirect
```

---

*See CRITICAL_UPDATES_COMPLETE.md for full documentation*
