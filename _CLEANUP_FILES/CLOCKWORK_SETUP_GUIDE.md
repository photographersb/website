# ⏰ Clockwork Setup & Usage Guide

**Installation Date**: February 3, 2026  
**Version**: 5.3.5  
**Status**: ✅ Installed & Configured

---

## 📊 What is Clockwork?

Clockwork is a development tool for profiling and debugging Laravel applications. It provides:

- **Server-side profiling** - Performance metrics and timing
- **Database query analysis** - SQL execution time and counts
- **Request/Response inspection** - Headers, cookies, session data
- **Error tracking** - Exceptions and errors with full stack traces
- **API monitoring** - Perfect for debugging AJAX and REST API calls
- **Browser extension** - Chrome/Firefox extension for easy access

---

## ✅ Installation Status

### Installed Components
- ✅ **Clockwork Package** (v5.3.5)
- ✅ **Configuration File** (`config/clockwork.php`)
- ✅ **.env Configuration** (CLOCKWORK_ENABLED=true)
- ✅ **Data Collection** (Stack traces, data sources enabled)

### Auto-Discovered Services
```
✅ barryvdh/laravel-dompdf
✅ itsgoingd/clockwork
✅ laravel/sail
✅ laravel/sanctum
✅ laravel/socialite
✅ laravel/tinker
✅ nesbot/carbon
✅ nunomaduro/collision
✅ nunomaduro/termwind
✅ spatie/laravel-ignition
```

---

## 🚀 Getting Started

### 1. **Install Browser Extension** (Recommended)

**Chrome**:
- Go to [Chrome Web Store](https://chrome.google.com/webstore)
- Search: "Clockwork"
- Click "Add to Chrome"

**Firefox**:
- Go to [Firefox Add-ons](https://addons.mozilla.org)
- Search: "Clockwork"
- Click "Add to Firefox"

### 2. **Access Clockwork API**

While your Laravel dev server is running:

```
Browser Extension:
  Click the extension icon in your browser toolbar
  
Web UI (if extension not installed):
  Visit: http://localhost:8000/__clockwork/
  
API Endpoint:
  GET: http://localhost:8000/__clockwork/api/requests/latest
  GET: http://localhost:8000/__clockwork/api/requests/[timestamp]
```

### 3. **Start Your Dev Server**

```bash
cd "c:\xampp\htdocs\Photographar SB"
php artisan serve --host=127.0.0.1 --port=8000
```

---

## 📋 Configuration

### .env Settings

```env
# Enable/Disable Clockwork
CLOCKWORK_ENABLED=true

# Stack trace collection (for debugging)
CLOCKWORK_COLLECT_STACK_TRACES=true

# Data source collection (cache, database, etc)
CLOCKWORK_COLLECT_DATA_SOURCES=true
```

### Key Config Options (config/clockwork.php)

#### Cache Monitoring
```php
'cache' => [
    'enabled' => true,           // Monitor cache operations
    'collect_queries' => true,    // Track cache queries
    'collect_values' => false,    // Collect cache values (use carefully)
]
```

#### Database Monitoring
```php
'database' => [
    'enabled' => true,              // Monitor database queries
    'collect_queries' => true,       // Collect all queries
    'collect_models_actions' => true, // Track model updates
    'slow_threshold' => 100,         // Mark queries > 100ms as slow
]
```

#### Request Monitoring
```php
'requests' => [
    'enabled' => true,      // Monitor HTTP requests
    'collect_headers' => true,
    'collect_post_data' => true,
    'collect_cookies' => true,
]
```

#### Performance Profiling
```php
'performance' => [
    'enabled' => true,           // Measure execution time
    'collect_stack_traces' => true, // Include stack traces
]
```

---

## 🔍 What Clockwork Monitors

### 1. **Performance Metrics**
- Total execution time
- Peak memory usage
- Database query time
- Cache operation time
- Route matching time

### 2. **Database Queries**
- SQL query text
- Execution time
- Number of rows affected
- Bindings/parameters
- Slow query detection

### 3. **HTTP Details**
- Request method, URL, IP
- Route information
- Query parameters
- POST/PUT data
- Cookies & session data
- Response headers & status

### 4. **Errors & Exceptions**
- Exception type & message
- Stack trace with file/line
- Triggering code context
- Exception chain

### 5. **Cache Operations**
- Cache hits/misses
- Cache key access
- Cache invalidation
- TTL information

### 6. **Authentication**
- Current user info
- Guards used
- Authorization checks
- CSRF token status

---

## 💡 Usage Tips

### API Testing with Clockwork

When testing your API endpoints:

1. **Make a request** to your API
2. **Open browser extension** - Click Clockwork icon
3. **View request details**:
   - Response time
   - Database queries executed
   - Errors/warnings
   - Stack traces

### Common Use Cases

#### 🔍 Finding Slow Queries
```
1. Open Clockwork after API request
2. Click "Database" tab
3. Sort by "Time" - slow queries highlight in red
4. Identify N+1 queries (repeated queries)
5. Add eager loading with relationships
```

#### 🐛 Debugging API Errors
```
1. Make request that triggers error
2. Open Clockwork - Click "Errors" tab
3. View full stack trace
4. Click file paths to see code context
5. Identify exact line causing issue
```

#### ⚡ Performance Optimization
```
1. Monitor all requests via Clockwork
2. Check memory usage & execution time
3. Identify bottlenecks
4. Add caching where appropriate
5. Use database indexes for slow queries
```

#### 🔐 Authentication Debugging
```
1. Make authenticated request
2. Open Clockwork - Click "Auth" tab
3. View current user & guard info
4. Check authorization results
5. Verify token/session state
```

---

## 🎯 Clockwork Dashboard Overview

### Tabs & Information

| Tab | Shows |
|-----|-------|
| **Overview** | Summary, execution time, memory, queries |
| **Timeline** | Event timeline with durations |
| **Database** | All database queries, time, bindings |
| **Cache** | Cache operations, hits, misses |
| **Emails** | Sent emails, recipients, content |
| **Events** | Dispatched events |
| **Log** | Application logs, debug messages |
| **Routes** | Route info, middleware used |
| **Auth** | Current user, guards, permissions |
| **Cookies** | Request/response cookies |
| **Session** | Session data and values |
| **Headers** | Request & response headers |
| **Environment** | App settings & environment |

---

## 🚨 Performance Warnings

### Slow Query Detection
Queries taking > 100ms are automatically marked as slow:
- Appears in red in Database tab
- Logged for analysis
- Helps identify missing indexes

### High Memory Usage
- Monitored continuously
- Peak memory shown in overview
- Helps identify memory leaks
- Context for optimization

### N+1 Query Detection
- Repeated queries for same data
- Often visible in loops
- Solution: Use eager loading
- Example fix: `with()` in queries

---

## 🔧 Advanced Configuration

### Modify Data Collection Thresholds

Edit `config/clockwork.php`:

```php
'database' => [
    'slow_threshold' => 50,  // Mark queries > 50ms as slow (default: 100)
]

'queue' => [
    'enabled' => true,
]

'cache' => [
    'collect_values' => true, // Collect all cache values (performance impact)
]
```

### Only Enable for Specific URLs

```php
// In config/clockwork.php
'only' => ['/api/*', '/admin/*'], // Only monitor these routes
'except' => ['/__clockwork/*'],     // Exclude these routes
```

---

## 📱 Browser Extension Features

### Clockwork Browser Extension Shows:
- ✅ Latest request details
- ✅ Request history list
- ✅ Search requests by URL
- ✅ Timeline visualization
- ✅ Database query analysis
- ✅ Real-time updates
- ✅ Copy request data
- ✅ Export profiling data

### Installation & Setup:
1. Install extension from store
2. Visit your Laravel app
3. Make an HTTP request
4. Click Clockwork icon in toolbar
5. View detailed profiling data

---

## 🔐 Security Notes

### Development Only
- ⚠️ Clockwork collects sensitive data
- ✅ Only enable in `APP_ENV=local`
- ✅ Automatically disabled on `APP_DEBUG=false`
- ✅ Not accessible from remote IPs by default

### Localhost Only by Default
```php
// Only accessible from:
- localhost
- 127.0.0.1
- *.local domains
- *.test domains
- *.wip domains
```

### Enable on Remote (Use with Caution)
```php
// In config/clockwork.php
'except_paths' => [],  // Remove localhost restriction
'except_ips' => [],    // Allow remote IPs
```

---

## 🐛 Troubleshooting

### Clockwork Not Appearing
```
1. Verify: APP_DEBUG=true in .env
2. Verify: CLOCKWORK_ENABLED=true in .env
3. Clear cache: php artisan cache:clear
4. Clear config: php artisan config:clear
5. Restart: php artisan serve
```

### Browser Extension Not Connecting
```
1. Check: Dev server running on localhost:8000
2. Check: Browser console for errors
3. Reinstall: Extension from app store
4. Try: Web UI at /__clockwork/
```

### High Performance Impact
```
1. Disable: collect_values in cache config
2. Disable: Stack traces collection if not needed
3. Reduce: Number of requests profiled
4. Use: except_paths to exclude non-critical routes
```

---

## 📚 Learn More

- **Official Docs**: https://underground.works/clockwork/
- **Browser Extension**: https://github.com/itsgoingd/clockwork
- **Laravel Integration**: https://github.com/itsgoingd/clockwork/blob/master/Clockwork/Support/Laravel/README.md

---

## ✨ Quick Start Checklist

- [x] ✅ Install Clockwork (v5.3.5)
- [x] ✅ Publish config file
- [x] ✅ Configure .env settings
- [ ] 📥 Install browser extension
- [ ] 🚀 Start Laravel dev server
- [ ] 🔍 Make a request to your app
- [ ] 👁️ Open Clockwork & view profiling data
- [ ] 📊 Analyze queries & performance
- [ ] 🔧 Optimize based on findings

---

**Clockwork is now ready for development profiling and debugging!** 🎉

Use it to identify bottlenecks, optimize queries, and fix bugs faster. The detailed profiling data will help you understand exactly what's happening in each request.

Happy debugging! 🐛
