(function (_, $) {
    'use strict';

    var queue = [];
    var recent = {};
    var flushTimer = null;
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
        talario_free_booking: true,
        talario_article_marketplace_click: true,
        talario_back: true
    };

    function counterId() {
        var value = _ && _.yandexMetrika && _.yandexMetrika.settings
            ? _.yandexMetrika.settings.id
            : '';
        var raw = String(value || '').trim();

        return /^\d{1,12}$/.test(raw) ? parseInt(raw, 10) : 0;
    }

    function compactPayload(payload) {
        var result = {};
        var key;

        payload = payload || {};
        for (key in payload) {
            if (!Object.prototype.hasOwnProperty.call(payload, key)) {
                continue;
            }

            if (payload[key] === undefined || payload[key] === null || payload[key] === '') {
                continue;
            }

            result[key] = payload[key];
        }

        return result;
    }

    function dedupeKey(name, payload) {
        var normalized = compactPayload(payload);
        var keys = Object.keys(normalized).sort();
        var parts = [name];
        var i;

        for (i = 0; i < keys.length; i += 1) {
            parts.push(keys[i] + '=' + String(normalized[keys[i]]));
        }

        return parts.join('|');
    }

    function shouldSend(name, payload) {
        var now = Date.now();
        var key = dedupeKey(name, payload);

        if (recent[key] && now - recent[key] < 1200) {
            return false;
        }

        recent[key] = now;
        return true;
    }

    function sendNow(eventData) {
        var id = counterId();

        if (!id || typeof window.ym !== 'function') {
            return false;
        }

        try {
            window.ym(id, 'reachGoal', eventData.name);
            return true;
        } catch (e) {
            return false;
        }
    }

    function flushQueue() {
        var pending = [];
        var i;

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

    function emit(name, payload) {
        var eventData;

        if (!allowedEvents[name] || !shouldSend(name, payload)) {
            return;
        }

        eventData = {
            name: name,
            payload: compactPayload(payload)
        };

        if (!sendNow(eventData)) {
            queue.push(eventData);

            if (!flushTimer) {
                flushTimer = window.setInterval(flushQueue, 500);
                window.setTimeout(function () {
                    if (flushTimer) {
                        window.clearInterval(flushTimer);
                        flushTimer = null;
                    }

                    queue = [];
                }, 5000);
            }
        }
    }

    function analyticsContext() {
        return window.talarioAnalyticsContext || {};
    }

    function currentProductId(scope) {
        var ctx = analyticsContext();
        var $scope = scope ? $(scope) : $(document);
        var productId = ctx.product_id
            || $scope.find('input[name*="[product_id]"]').first().val()
            || $scope.find('[data-ca-product-id]').first().attr('data-ca-product-id')
            || '';

        return parseInt(productId, 10) || 0;
    }

    function currentCompanyId() {
        var ctx = analyticsContext();
        return parseInt(ctx.company_id, 10) || 0;
    }

    function pagePath() {
        return window.location.pathname || '/';
    }

    function pagePayload(extra) {
        var payload = {
            product_id: currentProductId(),
            company_id: currentCompanyId(),
            path: pagePath()
        };
        var key;

        extra = extra || {};
        for (key in extra) {
            if (Object.prototype.hasOwnProperty.call(extra, key)) {
                payload[key] = extra[key];
            }
        }

        return payload;
    }

    function detectProductView() {
        var ctx = analyticsContext();
        var hasProductForm = $('form[name^="product_form_"], form[id^="product_form_"]').length > 0;
        var productId = currentProductId();

        if (ctx.controller === 'products' && ctx.mode === 'view' && productId) {
            emit('talario_product_open', pagePayload());
            return;
        }

        if (hasProductForm && productId) {
            emit('talario_product_open', pagePayload());
        }
    }

    function detectCheckoutPage() {
        var ctx = analyticsContext();
        var isCheckout = (ctx.controller === 'checkout' && ctx.mode === 'checkout')
            || $('form[name="checkout_form"], .ty-checkout-complete').length > 0;

        if (isCheckout && !ctx.order_id) {
            emit('talario_checkout_start', {
                path: pagePath()
            });
        }
    }

    function detectCompletedBooking() {
        var ctx = analyticsContext();

        if (!ctx.is_completed_order || !ctx.is_booking_order) {
            return;
        }

        if (ctx.is_free_order) {
            emit('talario_free_booking', {
                revenue: 0,
                currency: 'RUB'
            });
        }
    }

    $(document).on('click', '[data-talario-article-marketplace]', function () {
        emit('talario_article_marketplace_click', {
            path: pagePath()
        });
    });

    $(document).on('submit', 'form[name="search_form"]', function () {
        emit('talario_search_submit', {
            path: pagePath()
        });
    });

    $(document).on('change', '.cm-product-filters input, .cm-product-filters select', function () {
        emit('talario_filter_apply', {
            filter: this.name || this.id || ''
        });
    });

    $(document).on('click', '.cm-product-filters a.cm-ajax, .ty-product-filters__reset-button', function () {
        emit('talario_filter_apply', {
            filter: $(this).attr('href') || this.id || 'reset'
        });
    });

    $(document).on('change', 'select[name*="[product_options]"], input[name*="[product_options]"], [data-ca-variation-code]', function () {
        var $field = $(this);

        emit('talario_variant_select', pagePayload({
            option: $field.attr('name') || $field.attr('data-ca-variation-code') || $field.attr('id') || '',
            variation_id: $field.val() || $field.attr('data-ca-variation-code') || ''
        }));
    });

    $(document).on('click focus', '#date-range22, .date-picker-wrapper, .ec_select_slots_list, [id^="ec_timeslots"], [data-talario-schedule]', function () {
        if (scheduleSeen) {
            return;
        }

        scheduleSeen = true;
        emit('talario_schedule_open', pagePayload());
    });

    $(document).on('change', '.ec_select_slots_list, select[name*="[booking_slot]"], input[name*="[booking_slot]"]', function () {
        var value = String($(this).val() || '');

        if (!value) {
            return;
        }

        emit('talario_slot_select', pagePayload({
            slot: value,
            booking_date: $('[name*="[booking_date]"], [name*="[original_booking_date]"]').first().val() || '',
            resource_id: $('[name*="[resource_id]"]').first().val() || '',
            occurrence_id: $('[name*="[occurrence_id]"]').first().val() || ''
        }));
    });

    $(document).on('click', 'button[name^="dispatch[checkout.add"], input[name^="dispatch[checkout.add"], [id^="button_cart_"], [data-ca-dispatch*="checkout.add"]', function () {
        var $form = $(this).closest('form');
        var isBooking = $form.find('[name*="[booking_info]"], [name*="[booking_slot]"]').length > 0;

        if (isBooking) {
            emit('talario_booking_cta', pagePayload());
        }
    });

    if ($.ceEvent) {
        $.ceEvent('on', 'ce.ajaxdone', function (elms, inlineScripts, params, data) {
            var metrika = data && data.yandex_metrika ? data.yandex_metrika : {};
            var added = metrika.added || [];
            var i;

            for (i = 0; i < added.length; i += 1) {
                emit('talario_add_to_cart', {
                    product_id: parseInt(added[i].id, 10) || currentProductId(),
                    quantity: added[i].quantity || 1,
                    path: pagePath()
                });
            }
        });
    }

    window.addEventListener('popstate', function () {
        emit('talario_back', {
            path: pagePath()
        });
    });

    $(function () {
        detectProductView();
        detectCheckoutPage();
        detectCompletedBooking();
    });

}(Tygh, Tygh.$));
