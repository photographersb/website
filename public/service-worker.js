// Service Worker for Photographer SB PWA
const CACHE_NAME = 'photographer-sb-v7';
const RUNTIME_CACHE = 'photographer-sb-runtime-v7';
const ASSETS_TO_CACHE = [
  '/',
  '/index.html',
  '/manifest.json',
  '/placeholder-photographer.jpg'
];


// Install event: Cache essential assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Caching essential assets');
        return cache.addAll(ASSETS_TO_CACHE).catch(err => {
          console.log('Some assets failed to cache:', err);
          // Continue even if some assets fail
          return Promise.resolve();
        });
      })
  );
  self.skipWaiting();
});

// Activate event: Clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
            console.log('Service Worker: Deleting old cache', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event: Network-first strategy with fallback to cache
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip cross-origin requests
  if (url.origin !== location.origin) {
    return;
  }

  // Skip API requests - let them fail gracefully
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      fetch(request)
        .catch(() => {
          return new Response(
            JSON.stringify({ error: 'Offline - API unavailable' }),
            {
              status: 503,
              statusText: 'Service Unavailable',
              headers: { 'Content-Type': 'application/json' }
            }
          );
        })
    );
    return;
  }

  // Network-first strategy for all other requests
  event.respondWith(
    fetch(request)
      .then(response => {
        // Clone immediately to avoid "body already used" errors
        const responseToReturn = response.clone();
        
        // Cache the original response if successful
        if (response.ok) {
          caches.open(RUNTIME_CACHE).then(cache => {
            cache.put(request, response);
          });
        }
        
        return responseToReturn;
      })
      .catch(() => {
        // Fall back to cache on network error
        return caches.match(request)
          .then(cached => cached || createOfflineResponse());
      })
  );
});

// Create offline fallback response
function createOfflineResponse() {
  return new Response(
    `<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Offline - Photographer SB</title>
      <style>
        body {
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
          display: flex;
          align-items: center;
          justify-content: center;
          height: 100vh;
          margin: 0;
          background: #f3f4f6;
        }
        .offline-container {
          text-align: center;
          padding: 2rem;
          background: white;
          border-radius: 12px;
          box-shadow: 0 1px 3px rgba(0,0,0,0.1);
          max-width: 500px;
        }
        h1 { color: #1f2937; margin: 0 0 1rem 0; }
        p { color: #6b7280; margin: 0; }
      </style>
    </head>
    <body>
      <div class="offline-container">
        <h1>📡 You're Offline</h1>
        <p>Please check your internet connection to continue.</p>
        <p style="margin-top: 1rem; font-size: 0.9rem; color: #9ca3af;">
          Some pages may still be available from cache.
        </p>
      </div>
    </body>
    </html>`,
    {
      status: 503,
      statusText: 'Service Unavailable',
      headers: {
        'Content-Type': 'text/html; charset=utf-8',
        'Cache-Control': 'no-store'
      }
    }
  );
}

// Handle background sync (optional)
self.addEventListener('sync', event => {
  if (event.tag === 'sync-clicks') {
    event.waitUntil(syncClicks());
  }
});

async function syncClicks() {
  try {
    // This would sync any pending analytics if needed
    console.log('Background sync: syncing data');
  } catch (error) {
    console.log('Background sync failed:', error);
  }
}

// Message handler for client communication
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
