const OFFLINE_CACHE_NAME = 'offline';
const OFFLINE_URL = 'offline.html';
const LAST_UPDATE_KEY = 'last_offline_update';
const DELAY_IN_MS = 86400000; // One day: 60 * 60 * 24 * 1000

/**
 * Retrieves the last update timestamp for the offline cache.
 * @async
 * @returns {Promise<number>} The timestamp of the last update in milliseconds, or 0 if not found.
 */
async function getLastUpdateTime() {
  const cache = await caches.open(OFFLINE_CACHE_NAME);
  const response = await cache.match(LAST_UPDATE_KEY);
  if (!response) {
    return 0;
  }
  return parseInt(await response.text(), 10) || 0;
}

/**
 * Stores the current timestamp as the last update time in the cache.
 * @async
 * @param {number} timestampMs - The timestamp in milliseconds to store.
 * @returns {Promise<void>}
 */
async function setLastUpdateTime(timestampMs) {
  const cache = await caches.open(OFFLINE_CACHE_NAME);
  await cache.put(LAST_UPDATE_KEY, new Response(String(timestampMs), {
    headers: {
      'Content-Type': 'text/plain'
    }
  }));
}

/**
 * Adds the offline page to the cache if the update delay has passed.
 * @async
 * @returns {Promise<void>}
 */
async function addOfflinePageToCacheIfNeeded() {
  const nowMs = Date.now();
  const lastUpdateMs = await getLastUpdateTime();
  if (nowMs - lastUpdateMs < DELAY_IN_MS) {
    return;
  }
  const cache = await caches.open(OFFLINE_CACHE_NAME);

  // Add offline page to cache
  await cache.add(new Request(OFFLINE_URL, {
    cache: 'reload'
  }));
  await setLastUpdateTime(nowMs);
}
self.serviceWorkerAPI.addEventListener('install', event => {
  event.waitUntil(addOfflinePageToCacheIfNeeded());
  self.skipWaiting();
});
self.serviceWorkerAPI.addEventListener('fetch', event => {
  if (event.request.mode === 'navigate') {
    event.respondWith((async () => {
      try {
        const preloadResponse = await event.preloadResponse;
        if (preloadResponse) {
          return preloadResponse;
        }
        return await fetch(event.request);
      } catch {
        const cache = await caches.open(OFFLINE_CACHE_NAME);

        // Get an offline page from the cache if there is no internet
        return cache.match(OFFLINE_URL);
      }
    })());
  }
});
self.serviceWorkerAPI.addEventListener('message', event => {
  if (event.data === 'page_loaded') {
    (async () => {
      await addOfflinePageToCacheIfNeeded();
      await self.clients.claim();
    })();
  }
});