# 🔐 SECURITY QUICK REFERENCE GUIDE
## Essential Security Configuration for Production

---

# 1. CORS Configuration

**File**: `config/cors.php`

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    
    // ⚠️ CHANGE FROM '*' TO SPECIFIC DOMAINS
    'allowed_origins' => [
        env('APP_URL', 'https://photographar-sb.com'),
        'https://photographar-sb.com',
        'https://www.photographar-sb.com',
        // NO localhost or * in production
    ],

    'allowed_origins_patterns' => [],

    'allowed_methods' => ['*'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

Run:
```bash
php artisan config:cache
```

---

# 2. CSRF Token Configuration  

**File**: `config/session.php`

```php
'secure' => env('SESSION_SECURE_COOKIES', true), // HTTPS only
'http_only' => true, // JS cannot access
'same_site' => 'strict', // Prevent CSRF
```

---

# 3. Remove Sensitive Data from Logs

**File**: `config/logging.php`

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single'],
        'ignore_exceptions_unless_reporting' => false,
    ],

    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'permission' => 0644, // Read-only
        // ADD THIS:
        'processors' => [
            [
                'processor' => \Monolog\Processor\PsrLogMessageProcessor::class,
            ],
        ],
    ],
],

// Add at bottom of file:
'sanitize_keys' => [
    'password',
    'passwd',
    'pw',
    'secret',
    'token',
    'access_token',
    'auth_token',
    'card_number',
    'ccv',
    'cvv',
    'credit_card',
    'pin',
    'payment_token',
],
```

---

# 4. Helmet Security Headers

**File**: `app/Http/Middleware/SecurityHeaders.php`

```php
<?php
namespace App\Http\Middleware;

use Closure;

class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');
        
        // Prevent XSS attacks
        $response->header('X-XSS-Protection', '1; mode=block');
        
        // Referrer policy
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy
        $response->header('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net",
            "img-src 'self' data: https:",
            "font-src 'self' cdn.jsdelivr.net",
        ]));
        
        // HSTS (HTTPS Strict Transport Security)
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
```

Use in `app/Http/Kernel.php`:
```php
protected $middleware = [
    // ... existing
    \App\Http\Middleware\SecurityHeaders::class,
];
```

---

# 5. API Rate Limiting Configuration

**File**: `config/api.php` (create if doesn't exist)

```php
return [
    'rate_limiting' => [
        // Authentication endpoints (strict)
        'auth' => [
            'register' => '5,60', // 5 per minute
            'login' => '10,60', // 10 per minute
            'password_reset' => '3,3600', // 3 per hour
        ],
        
        // Public endpoints (moderate)
        'public' => [
            'competitions' => '100,60',
            'photographers' => '200,60',
            'events' => '100,60',
        ],
        
        // User endpoints (generous)
        'user' => [
            'default' => '1000,3600',
        ],
        
        // Payment endpoints (very strict)
        'payment' => [
            'initiate' => '10,3600', // 10 per hour
            'refund' => '3,3600', // 3 per hour
        ],
    ],
];
```

Use in `routes/api.php`:
```php
Route::post('/auth/register', ...)
    ->middleware('throttle:auth.register');

Route::post('/payments/refund', ...)
    ->middleware('throttle:payment.refund');
```

---

# 6. SQL Injection Prevention

Always use parameterized queries:

```php
// ❌ BAD
$users = DB::select("SELECT * FROM users WHERE email = '$email'");

// ✅ GOOD
$users = User::where('email', $email)->get();

// ✅ ALSO GOOD
$users = DB::select('SELECT * FROM users WHERE email = ?', [$email]);
```

---

# 7. XSS Prevention

Always escape output in Vue:

```vue
<!-- ❌ BAD - Renders HTML -->
<div v-html="userInput"></div>

<!-- ✅ GOOD - Escapes -->
<div>{{ userInput }}</div>

<!-- ✅ If you need HTML, sanitize -->
<div v-html="sanitizeHtml(userInput)"></div>
```

Sanitize in controller:
```php
use Illuminate\Support\HtmlString;

$clean = \Illuminate\Support\Str::of($userInput)
    ->replace('<script', '&lt;script')
    ->replace('javascript:', 'js:');
```

---

# 8. Mass Assignment Protection

```php
// In Models
protected $fillable = [
    'name',
    'email',
    // List exactly what can be mass-assigned
];

// Block everything else from being set
protected $guarded = [];  // ❌ DANGEROUS
protected $guarded = ['*']; // ✅ SAFE - block all except fillable
```

---

# 9. Method Spoofing Protection

```php
// In routes, use explicit methods
Route::delete('/users/{id}', ...); // Must be DELETE method
Route::put('/users/{id}', ...);    // Must be PUT method

// NEVER do this:
Route::get('/users/{id}/delete', ...); // ❌ DELETE via GET
```

---

# 10. Sanctum Token Security

**File**: `config/sanctum.php`

```php
return [
    // Token expiration
    'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 60), // 60 minutes
    
    // Limit token usage by IP
    'guard' => ['web'], // Use session guard also
    
    // Middleware for protecting API
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

Force re-authentication after role change:
```php
auth()->user()->tokens()->delete(); // Revoke all tokens
return response()->json(['message' => 'Please re-login']);
```

---

# 11. Create Security Audit Log

```php
// database/migrations/create_security_audit_table.php
Schema::create('security_audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained();
    $table->string('event'); // 'login', 'failed_login', 'permission_change', etc
    $table->string('ip_address');
    $table->string('user_agent')->nullable();
    $table->json('details')->nullable(); // Extra data
    $table->timestamp('created_at');
    
    $table->index(['user_id', 'event', 'created_at']);
});
```

Log security events:
```php
// When critical action occurs
SecurityAuditLog::create([
    'user_id' => auth()->id(),
    'event' => 'permission_change',
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'details' => [
        'from_role' => 'user',
        'to_role' => 'admin',
        'changed_by' => auth()->id(),
    ],
]);
```

---

# 12. Environment Security

**File**: `.env`

```bash
# ✅ Production Settings
APP_ENV=production
APP_DEBUG=false             # Never true in production
APP_URL=https://photographar-sb.com

# Security Keys
APP_KEY=<generate with: php artisan key:generate>
JWT_SECRET=<generate strong key>

# HTTPS Only
SESSION_SECURE_COOKIES=true
TRUSTED_PROXIES=<your load balancer IP>

# Database
DB_CONNECTION=mysql
DB_HOST=<encrypted value>
DB_PASSWORD=<very strong password>

# Mail (for notifications)
MAIL_FROM_ADDRESS=noreply@photographar-sb.com
MAIL_FROM_NAME="Photographer SB"

# Payment Gateway (should use env vars, NOT hardcoded)
BKASH_APP_KEY=<from secure vault>
BKASH_APP_SECRET=<from secure vault>
```

**Never commit `.env` to git**:
```bash
git check-ignore .env # Verify .env is ignored
```

---

# 13. Enable Monitoring & Alerts

```php
// config/logging.php
'channels' => [
    'sentry' => [
        'driver' => 'sentry',
        'level' => 'error',
        'dsn' => env('SENTRY_LARAVEL_DSN'),
    ],
];

// app/Exceptions/Handler.php
public function register()
{
    $this->reportable(function (Throwable $e) {
        if ($this->shouldReport($e)) {
            \Sentry\captureException($e);
        }
    });
}
```

Install Sentry:
```bash
composer require sentry/sentry-laravel:^4.0

cat > .env << 'EOF'
SENTRY_LARAVEL_DSN=https://...@sentry.io/...
SENTRY_ENVIRONMENT=production
SENTRY_TRACES_SAMPLE_RATE=0.1
EOF
```

---

# 14. API Key Rotation Policy

Create keys that expire:

```php
// Migration
Schema::table('personal_access_tokens', function (Blueprint $table) {
    $table->timestamp('expires_at')->nullable();
    $table->string('name')->default('API Token');
});

// Command to notify about expiring tokens
php artisan schedule:add-command 'notify:expiring-api-keys' --monthly

// Logic to force re-generation
if ($token->expires_at && $token->expires_at < now()) {
    auth()->logout();
    return response()->json(['message' => 'API token expired'], 401);
}
```

---

# 15. Firewall Rules

For production server:

```bash
# Allow only specific IPs to reach admin routes (optional)
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 80    # HTTP (for redirect)
sudo ufw allow 443   # HTTPS
sudo ufw allow 22    # SSH (limit to known IPs)
sudo ufw allow from <YOUR_IP> to any port 22

# DDoS protection
sudo apt-get install fail2ban
```

---

# 🚀 DEPLOYMENT CHECKLIST

Before Deploying to Production:

- [ ] All 15 security settings configured
- [ ] HTTPS certificate installed and working
- [ ] APP_DEBUG=false in .env
- [ ] CORS whitelist configured (not `*`)
- [ ] CSRF protection enabled
- [ ] Security headers middleware added
- [ ] Rate limiting on sensitive endpoints
- [ ] Sentry/error monitoring enabled
- [ ] Database backups automated
- [ ] Logs monitored and rotated
- [ ] API keys scoped to specific endpoints
- [ ] Payment gateway credentials in secure vault
- [ ] Email configured for notifications
- [ ] Session timeout reasonable (5-60 minutes)
- [ ] 2FA available for admin users

---

# 📞 INCIDENT RESPONSE

If security breach detected:

1. **Immediate** (< 5 minutes):
   - Shut down affected service if necessary
   - Revoke all API tokens
   - Notify security team
   
2. **Short term** (1-6 hours):
   - Assess scope of breach
   - Rotate all secrets/credentials
   - Review audit logs
   - Patch vulnerability

3. **Medium term** (1-7 days):
   - Notify affected users
   - Reset all passwords
   - Review all access logs
   - Post-mortem analysis

4. **Long term**:
   - Update security policies
   - Add additional monitoring
   - Conduct security training
   - Schedule penetration test

---

**Security is an ongoing process, not a one-time fix.**

Review this guide quarterly and update as needed.
