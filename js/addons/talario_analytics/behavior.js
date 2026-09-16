if (typeof Tygh !== 'undefined' && Tygh.$) {
(function (_, $) {
    'use strict';

    var recent = {};
    var scheduleSeen = false;

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

    // Product decision (2026-09-16): behavior goals are not a separate tracker.
    // They are emitted only through the already-active native rus_yandex_metrika
    // runtime, without new cookies, identifiers, payload parameters, or a new
    // consent UI. If native Metrika is unavailable, behavior events are dropped.
    function nativeMetrikaReady() {
        return !!(
            _ &&
            _.yandexMetrika &&
            _.yandexMetrika.provider &&
            _.yandexMetrika.provider.id === 'default' &&
            counterId() &&
            typeof window.ym === 'function'
        );
    }

    function shouldSend(name) {
        var now = Date.now();

        if (recent[name] && now - recent[name] < 600) {
            return false;
        }

        recent[name] = now;
        return true;
    }

    function emit(name) {
        var id;

        if (!allowedEvents[name] || !nativeMetrikaReady() || !shouldSend(name)) {
            return;
        }

        id = counterId();

        try {
            window.ym(id, 'reachGoal', name);
        } catch (e) {}
    }

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
