const {
  currentScript
} = document;
if ('serviceWorker' in navigator && currentScript !== null && 'src' in currentScript) {
  const currentScriptUrl = new URL(currentScript.src);
  const currentLocation = Object.fromEntries(currentScriptUrl.searchParams.entries()).current_location;
  if (currentLocation) {
    const serviceWorkerUrl = new URL('service_worker.js', currentLocation.endsWith('/') ? currentLocation : currentLocation + '/');

    // Extendable via add-ons
    for (const [searchKey, searchValue] of currentScriptUrl.searchParams) {
      serviceWorkerUrl.searchParams.set(searchKey, searchValue);
    }
    const serviceWorkerUrlHref = serviceWorkerUrl.href;
    const isRegistered = async () => (await navigator.serviceWorker.getRegistrations()).some(reg => reg.active && new URL(reg.active.scriptURL).pathname === serviceWorkerUrl.pathname);
    const onReady = () => {
      if (navigator.serviceWorker.controller !== null) {
        navigator.serviceWorker.controller.postMessage('page_loaded');
      }
    };
    const checkAndRegisterServiceWorker = async () => {
      if (await isRegistered()) {
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', onReady);
        } else {
          onReady();
        }
        return;
      }

      // service_worker.js MUST be located in the root. Absolute path required
      navigator.serviceWorker.register(serviceWorkerUrlHref);
    };
    checkAndRegisterServiceWorker();
  }
}