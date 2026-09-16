if (typeof Tygh !== 'undefined' && Tygh.$) {
(function (_, $) {
    'use strict';

    var queue = [];
    var recent = {};
    var flushTimer = null;
    var scheduleSeen = false;
    var consentGranted = false;

    var allowedEvents = {
        talario_search_submit: true,
        talario_filter_apply: true,
        talario_product_open: true,
        talario_variant_select: true,
        talario_schedule_open: true,
        talario_slot_select: true,
        talario_booking_cta: true,
        talario_add_to_cart: true,
        talario_checkout_start: true,
        talario_back: true
    };

    function counterId() {
        var value = _ && _.yandexMetrika && _.yandexMetrika.settings
            ? _.yandexMetrika.settings.id
            : '';
        var raw = String(value || '').trim();

        return /^\d{1,12}$/.test(raw) ? parseInt(raw, 10) : 0;
    }

    function readConsentState() {
        try {
            if (typeof window.klaro === 'undefined' || typeof window.klaro.getManager !== 'function') {
                return false;
            }

            var manager = window.klaro.getManager();

            return !!(manager && manager.states && manager.states.yandex_metrika === true);
        } catch (e) {
            return false;
        }
    }

    function shouldSend(name) {
        var now = Date.now();

        if (recent[name] && now - recent[name] < 600) {
            return false;
        }

        recent[name] = now;
        return true;
    }

    function sendNow(name) {
        var id = counterId();

        if (!consentGranted || !id || typeof window.ym !== 'function') {
            return false;
        }

        try {
            window.ym(id, 'reachGoal', name);
            return true;
        } catch (e) {
            return false;
        }
    }

    function clearQueue() {
        queue = [];

        if (flushTimer) {
            window.clearInterval(flushTimer);
            flushTimer = null;
        }
    }

    function flushQueue() {
        var pending = [];
        var i;

        if (!consentGranted) {
            return;
        }

        for (i = 0; i < queue.length; i += 1) {
            if (!sendNow(queue[i])) {
                pending.push(queue[i]);
            }
        }

        queue = pending;

        if (!queue.length && flushTimer) {
            window.clearInterval(flushTimer);
            flushTimer = null;
        }
    }

    function emit(name) {
        if (!consentGranted || !allowedEvents[name] || !shouldSend(name)) {
            return;
        }

        if (!sendNow(name)) {
            queue.push(name);

            if (!flushTimer) {
                flushTimer = window.setInterval(flushQueue, 500);
                window.setTimeout(clearQueue, 5000);
            }
        }
    }

    consentGranted = readConsentState();

    $.ceEvent('on', 'ce.gdpr_cookie_on_accept_yandex_metrika', function () {
        consentGranted = true;
        flushQueue();
    });

    $.ceEvent('on', 'ce.gdpr_cookie_on_decline_yandex_metrika', function () {
        consentGranted = false;
        clearQueue();
    });

    $(document).on('submit', 'form[name="search_form"]', function () {
        emit('talario_search_submit');
    });

    $(document).on('change', '.cm-product-filters input, .cm-product-filters select', function () {
        emit('talario_filter_apply');
    });

    $(document).on('click', '.cm-product-filters a.cm-ajax, .ty-product-filters__reset-button', function () {
        emit('talario_filter_apply');
    });

    $(document).on('click', 'a[href*="products.view"][href*="product_id="]', function () {
        emit('talario_product_open');
    });

    $(document).on('change', 'select[name*="[product_options]"], input[name*="[product_options]"], [data-ca-variation-code]', function () {
        emit('talario_variant_select');
    });

    $(document).on('click focus', '#date-range22, .date-picker-wrapper, .ec_select_slots_list, [id^="ec_timeslots"]', function () {
        if (scheduleSeen) {
            return;
        }

        scheduleSeen = true;
        emit('talario_schedule_open');
    });

    $(document).on('change', '.ec_select_slots_list, select[name*="[booking_slot]"]', function () {
        if (!String($(this).val() || '')) {
            return;
        }

        emit('talario_slot_select');
    });

    $(document).on('click', 'button[name^="dispatch[checkout.add"], input[name^="dispatch[checkout.add"], [id^="button_cart_"]', function () {
        var isBooking = $(this).closest('form').find('[name*="[booking_info]"]').length > 0;

        emit(isBooking ? 'talario_booking_cta' : 'talario_add_to_cart');
    });

    $(document).on('click', 'a[href*="checkout.checkout"], button[name^="dispatch[checkout.checkout"], input[name^="dispatch[checkout.checkout"]', function () {
        emit('talario_checkout_start');
    });

    window.addEventListener('popstate', function () {
        emit('talario_back');
    });

}(Tygh, Tygh.$));
}
