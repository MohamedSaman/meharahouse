/**
 * Meharahouse Service Worker
 * Strategy:
 *   - Static assets (CSS, JS, fonts, images): Cache-first, fallback to network
 *   - Page navigations: Network-first, fallback to cache, then offline.html
 *   - API / dynamic routes: Network-first, no cache
 *
 * Bump CACHE_VERSION whenever you deploy significant static asset changes.
 */

const CACHE_VERSION = 'v1';
const STATIC_CACHE  = `meharahouse-static-${CACHE_VERSION}`;
const PAGE_CACHE    = `meharahouse-pages-${CACHE_VERSION}`;

// Assets to pre-cache on service worker install
const PRECACHE_ASSETS = [
    '/offline.html',
    '/favicon.ico',
];

// URL patterns that should NEVER be cached
const NEVER_CACHE_PATTERNS = [
    /\/admin\//,
    /\/staff\//,
    /\/livewire\//,
    /\/api\//,
    /\/_debugbar\//,
    /\/sanctum\//,
    /logout/,
    /login/,
    /register/,
    /password/,
    /csrf-cookie/,
];

// Static asset extensions to cache-first
const STATIC_ASSET_EXTENSIONS = /\.(css|js|woff2?|ttf|eot|otf|svg|png|jpe?g|gif|ico|webp|avif)(\?.*)?$/i;

// ─── INSTALL ──────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// ─── ACTIVATE ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    const validCaches = [STATIC_CACHE, PAGE_CACHE];

    event.waitUntil(
        caches.keys()
            .then((cacheNames) =>
                Promise.all(
                    cacheNames
                        .filter((name) => !validCaches.includes(name))
                        .map((name) => caches.delete(name))
                )
            )
            .then(() => self.clients.claim())
    );
});

// ─── FETCH ────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin or known CDN requests
    if (url.origin !== self.location.origin &&
        !url.hostname.includes('fonts.googleapis.com') &&
        !url.hostname.includes('fonts.gstatic.com')) {
        return;
    }

    // Never cache admin, staff, auth, API, or Livewire endpoints
    if (NEVER_CACHE_PATTERNS.some((pattern) => pattern.test(url.pathname))) {
        return;
    }

    // Non-GET requests bypass the service worker completely
    if (request.method !== 'GET') {
        return;
    }

    // ── Strategy: Cache-first for static assets ────────────────────────────
    if (STATIC_ASSET_EXTENSIONS.test(url.pathname) ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com')) {
        event.respondWith(cacheFirstStrategy(request, STATIC_CACHE));
        return;
    }

    // ── Strategy: Network-first for HTML page navigations ─────────────────
    if (request.mode === 'navigate') {
        event.respondWith(networkFirstPageStrategy(request));
        return;
    }
});

// ─── CACHE-FIRST STRATEGY ─────────────────────────────────────────────────────
async function cacheFirstStrategy(request, cacheName) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        // For images, return a transparent placeholder rather than nothing
        if (STATIC_ASSET_EXTENSIONS.test(new URL(request.url).pathname)) {
            return new Response('', { status: 408, statusText: 'Offline' });
        }
        throw error;
    }
}

// ─── NETWORK-FIRST PAGE STRATEGY ──────────────────────────────────────────────
async function networkFirstPageStrategy(request) {
    try {
        const networkResponse = await fetch(request);

        // Only cache successful, non-redirected HTML responses
        if (networkResponse.ok && networkResponse.type === 'basic') {
            const cache = await caches.open(PAGE_CACHE);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        // Try the page cache first
        const cachedPage = await caches.match(request);
        if (cachedPage) {
            return cachedPage;
        }

        // Fall back to the offline page
        const offlinePage = await caches.match('/offline.html');
        if (offlinePage) {
            return offlinePage;
        }

        // Absolute last resort
        return new Response(
            '<html><body><h1>You are offline</h1><p>Please check your connection and try again.</p></body></html>',
            { status: 503, headers: { 'Content-Type': 'text/html' } }
        );
    }
}
