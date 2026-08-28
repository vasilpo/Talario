(function () {
    'use strict';

    var jivoHidden = false;
    var updateScheduled = false;

    function isMobileViewport() {
        return window.matchMedia('(max-width: 1023px)').matches;
    }

    function isVisible(element) {
        if (!element) {
            return false;
        }

        var style = window.getComputedStyle(element);
        var rect = element.getBoundingClientRect();

        return style.display !== 'none'
            && style.visibility !== 'hidden'
            && rect.width > 0
            && rect.height > 0;
    }

    function isCartOverlayOpen() {
        if (!isMobileViewport()) {
            return false;
        }

        var elements = document.querySelectorAll(
            '.cm-product-notification-body, .ty-dropdown-box__content--cart'
        );

        return Array.prototype.some.call(elements, isVisible);
    }

    function updateJivoVisibility() {
        var shouldHide = isCartOverlayOpen();

        if (shouldHide && !jivoHidden && typeof window.jivo_destroy === 'function') {
            window.jivo_destroy();
            jivoHidden = true;
            return;
        }

        if (!shouldHide && jivoHidden && typeof window.jivo_init === 'function') {
            window.jivo_init();
            jivoHidden = false;
        }
    }

    function scheduleUpdate() {
        if (updateScheduled) {
            return;
        }

        updateScheduled = true;

        window.requestAnimationFrame(function () {
            updateScheduled = false;
            updateJivoVisibility();
        });
    }

    function start() {
        if (!document.body) {
            return;
        }

        var observer = new MutationObserver(scheduleUpdate);

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });

        window.addEventListener('resize', scheduleUpdate);
        scheduleUpdate();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
}());
