/**
 * Gema∞ Service Worker - Online-First Strategy
 * Always tries network first, falls back to cache only when offline
 */

const CACHE_NAME = 'gema8-v1';
const STATIC_ASSETS = [
    '/assets/icons/icon-192x192.png',
    '/assets/icons/icon-512x512.png'
];

// Install: Cache minimal static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate: Clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: Online-first strategy
self.addEventListener('fetch', (event) => {
    const request = event.request;
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip cross-origin requests (CDNs, fonts, etc.)
    if (!request.url.startsWith(self.location.origin)) {
        return;
    }
    
    // Skip API requests - always go to network
    if (request.url.includes('/api/')) {
        return;
    }
    
    event.respondWith(
        // Try network first
        fetch(request)
            .then(response => {
                // Clone and cache successful responses for offline fallback
                if (response.ok) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Network failed, try cache
                return caches.match(request).then(cachedResponse => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    
                    // Return offline page for navigation requests
                    if (request.mode === 'navigate') {
                        return new Response(
                            `<!DOCTYPE html>
                            <html>
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Offline - Gema∞</title>
                                <style>
                                    * { font-family: 'Inter', system-ui, sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
                                    body { 
                                        min-height: 100vh; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center;
                                        background: linear-gradient(180deg, #fafbff 0%, #f8fafc 100%);
                                        padding: 1rem;
                                    }
                                    .container { 
                                        text-align: center; 
                                        max-width: 400px;
                                        padding: 2rem;
                                        background: white;
                                        border-radius: 1.25rem;
                                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                                    }
                                    .icon { 
                                        font-size: 4rem; 
                                        margin-bottom: 1rem;
                                    }
                                    h1 { 
                                        font-size: 1.5rem; 
                                        color: #1e293b; 
                                        margin-bottom: 0.5rem;
                                    }
                                    p { 
                                        color: #64748b; 
                                        margin-bottom: 1.5rem;
                                    }
                                    button {
                                        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                                        color: white;
                                        border: none;
                                        padding: 0.75rem 1.5rem;
                                        border-radius: 0.75rem;
                                        font-weight: 500;
                                        cursor: pointer;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="container">
                                    <div class="icon">📡</div>
                                    <h1>You're offline</h1>
                                    <p>Gema∞ requires an internet connection to translate and learn. Please check your connection and try again.</p>
                                    <button onclick="location.reload()">Try Again</button>
                                </div>
                            </body>
                            </html>`,
                            { headers: { 'Content-Type': 'text/html' } }
                        );
                    }
                    
                    // Return empty response for other requests
                    return new Response('', { status: 503 });
                });
            })
    );
});
