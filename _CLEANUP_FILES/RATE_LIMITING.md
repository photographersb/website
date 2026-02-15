# Rate Limiting Configuration

## Overview
Rate limiting has been implemented across all critical API endpoints to prevent spam, brute force attacks, and resource abuse.

## Rate Limits by Endpoint

### Authentication Endpoints
| Endpoint | Limit | Duration | Reason |
|----------|-------|----------|--------|
| POST /auth/login | 5 requests | 1 minute | Prevent brute force attacks |
| POST /auth/register | 5 requests | 1 minute | Prevent account spam |
| POST /auth/forgot-password | 3 requests | 60 minutes | Prevent email flooding |
| POST /auth/reset-password | 3 requests | 60 minutes | Prevent password reset abuse |
| POST /auth/verify-email | 10 requests | 60 minutes | Allow reasonable verification attempts |

### User Actions
| Endpoint | Limit | Duration | Reason |
|----------|-------|----------|--------|
| POST /bookings/inquiry | 10 requests | 1 minute | Prevent inquiry spam |
| POST /reviews | 5 requests | 60 minutes | Prevent review flooding |
| POST /events/{id}/rsvp | 20 requests | 60 minutes | Allow normal RSVP behavior |

### Competition Actions
| Endpoint | Limit | Duration | Reason |
|----------|-------|----------|--------|
| POST /competitions/{id}/submissions | 10 requests | 60 minutes | Reasonable submission rate |
| PUT /competitions/{id}/submissions/{id} | 10 requests | 60 minutes | Reasonable edit rate |
| POST /vote | 60 requests | 60 minutes | 1 vote per minute average |
| DELETE /vote | 60 requests | 60 minutes | Match voting rate |
| POST /score (judges) | 30 requests | 60 minutes | Judge scoring rate |

## Response Format

### When Rate Limit Exceeded (429 Too Many Requests)
```json
{
  "status": "error",
  "message": "Too many requests. Please slow down and try again later.",
  "retry_after": "45 seconds"
}
```

### Headers Included
- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Requests remaining
- `Retry-After`: Seconds until next request allowed

## Implementation Details

### Laravel Throttle Middleware
Using Laravel's built-in `throttle` middleware:
```php
Route::post('/reviews', [ReviewController::class, 'store'])
    ->middleware('throttle:5,60');
// Format: throttle:maxAttempts,decayMinutes
```

### Custom Middleware
Created `CustomThrottleRequests` middleware to provide user-friendly error messages instead of default Laravel response.

## Testing Rate Limits

### Manual Testing
```bash
# Test review rate limit (should fail after 5 requests in 1 hour)
for i in {1..6}; do
  curl -X POST http://localhost:8000/api/v1/reviews \
    -H "Authorization: Bearer YOUR_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"photographer_id":1,"rating":5,"comment":"Test review"}'
done
```

### Automated Testing
```php
// In tests/Feature/RateLimitTest.php
public function test_review_rate_limit()
{
    $user = User::factory()->create();
    
    // Make 5 successful requests
    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($user)
            ->postJson('/api/v1/reviews', $validData)
            ->assertStatus(201);
    }
    
    // 6th request should be rate limited
    $this->actingAs($user)
        ->postJson('/api/v1/reviews', $validData)
        ->assertStatus(429)
        ->assertJson(['status' => 'error']);
}
```

## Adjusting Rate Limits

### Per Environment
Create `.env` variables:
```env
RATE_LIMIT_LOGIN=5
RATE_LIMIT_REVIEW=5
RATE_LIMIT_BOOKING=10
```

Then reference in routes:
```php
Route::post('/reviews', [ReviewController::class, 'store'])
    ->middleware('throttle:' . env('RATE_LIMIT_REVIEW', 5) . ',60');
```

### Per User Role
For premium users or admins:
```php
Route::middleware(['auth:sanctum', 'role:premium'])->group(function () {
    Route::post('/bookings/inquiry', [BookingController::class, 'createInquiry'])
        ->middleware('throttle:50,1'); // Higher limit for premium users
});
```

## Monitoring

### Log Rate Limit Hits
Add to `CustomThrottleRequests`:
```php
protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
{
    Log::warning('Rate limit exceeded', [
        'user_id' => auth()->id(),
        'ip' => $request->ip(),
        'endpoint' => $request->path(),
        'max_attempts' => $maxAttempts,
    ]);
    
    // ... return response
}
```

### Database Tracking (Optional)
Create `rate_limit_violations` table to track repeat offenders for potential IP blocking.

## Best Practices

1. **Be Generous with Read Operations**: Only limit write/mutation operations
2. **Inform Users**: Display remaining requests in UI when possible
3. **Different Limits for Different Actions**: Critical actions (login) should have stricter limits
4. **Consider User Roles**: Premium/verified users may need higher limits
5. **Monitor and Adjust**: Review logs regularly to adjust limits based on actual usage

## Security Notes

- Rate limiting is **PER USER** for authenticated routes (uses user ID)
- Rate limiting is **PER IP** for public routes (uses IP address)
- Combining with CAPTCHA on login after 3 failed attempts is recommended
- Consider implementing progressive delays (exponential backoff)

## Bypass for Testing

During development, you can temporarily disable rate limiting:
```php
// In RouteServiceProvider or specific routes
if (app()->environment('local')) {
    // No throttle in local environment
} else {
    Route::middleware('throttle:5,1');
}
```

Or via `.env`:
```env
RATE_LIMIT_ENABLED=false
```

## Future Enhancements

1. **Redis Backend**: For distributed rate limiting across multiple servers
2. **Dynamic Limits**: Adjust based on user behavior/reputation
3. **IP Blacklist**: Auto-block IPs exceeding limits repeatedly
4. **Notification System**: Alert admins of potential abuse
5. **Rate Limit Dashboard**: Visual analytics of rate limit hits
