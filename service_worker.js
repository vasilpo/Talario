// service_worker.js MUST be located in the root

const url = new URL(location.href);
const scriptsParam = url.searchParams.get('scripts');

// Extendable via add-ons
const eventHandlers = {
    install: [],
    activate: [],
    message: [],
    fetch: [],
    sync: [],
    push: [],
};

self.serviceWorkerAPI = {
    /**
     * Adds a custom event handler for a given event type.
     * @param {string} eventType
     * @param {Function} handler
     */
    addEventListener: (eventType, handler) => {
        if (!(eventType in eventHandlers) || typeof handler !== 'function') {
            return;
        }
        eventHandlers[eventType].push(handler);
    },
};

Object.keys(eventHandlers).forEach((eventType) => {
    self.addEventListener(eventType, (event) => {
        for (const handler of eventHandlers[eventType]) {
            try {
                handler(event);
            } catch {
                // Ignore errors in individual handlers
            }
        }
    });
});

if (scriptsParam) {
    const scripts = scriptsParam.split(',');
    importScripts(...scripts);
}

self.addEventListener('install', () => {
    // Activate new service worker immediately without waiting for old tabs to close
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    // Take control of all pages without requiring a page reload
    event.waitUntil(self.clients.claim());
});
