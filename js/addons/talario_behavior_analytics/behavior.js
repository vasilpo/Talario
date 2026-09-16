(function (_, $) {
    'use strict';

    var COUNTER_ID = numericCounterId(_.talario_behavior_counter_id);
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
        talario_back: true
    };

    function numericCounterId(value) {
        var raw = String(value || '').trim();
        return /^\d{1,12}$/.test(raw) ? parseInt(raw, 10) : 0;
    }

    function safeToken(value, maxLength) {
        var raw = String(value || '').trim();
        var limit = maxLength || 64;
        if (!raw || raw.length > limit || !/^[A-Za-z0-9_.:-]+$/.test(raw)) {
            return '';
        }
        return raw;
    }

    function numericId(value) {
        var raw = String(value || '').trim();
        return /^\d{1,12}$/.test(raw) ? raw : '';
    }

    function currentPageType() {
        if ($('form[name^="product_form_"] input[name*="[product_id]"]').length) {
            return 'product';
        }
        if ($('form[name="checkout_form"], .ty-checkout, .litecheckout').length) {
            return 'checkout';
        }
        if ($('form[name="search_form"]').length) {
            return 'search';
        }
        if ($('.cm-product-filters').length) {
            return 'catalog';
        }
        return 'other';
    }

    function productIdFromElement(element) {
        var $element = $(element);
        var dataId = numericId($element.data('ca-product-id') || $element.data('ca-productid') || '');
        if (dataId) {
            return dataId;
        }

        var $product = $element.closest('[data-ca-product-id], [id^="product_main_info_"], form');
        dataId = numericId($product.data('ca-product-id') || '');
        if (dataId) {
            return dataId;
        }

        var idMatch = String($product.attr('id') || '').match(/(\d{1,12})$/);
        if (idMatch) {
            return idMatch[1];
        }

        var hiddenId = numericId($element.closest('form').find('input[name*="[product_id]"]').first().val() || '');
        if (hiddenId) {
            return hiddenId;
        }

        return '';
    }

    function filterIdFromElement(element) {
        var id = String($(element).closest('[id^="content_"], [id^="sw_content_"]').attr('id') || '');
        var match = id.match(/_(\d{1,12})$/);
        return match ? match[1] : '';
    }

    function buildParams(extra) {
        var params = {
            page_type: currentPageType()
        };
        var productId;
        var filterId;
        var optionId;

        extra = extra || {};

        productId = numericId(extra.product_id || '');
        filterId = numericId(extra.filter_id || '');
        optionId = numericId(extra.option_id || '');

        if (productId) {
            params.product_id = productId;
        }
        if (filterId) {
            params.filter_id = filterId;
        }
        if (optionId) {
            params.option_id = optionId;
        }

        return params;
    }

    function dedupeKey(name, params) {
        return [
            name,
            params.page_type || '',
            params.product_id || '',
            params.filter_id || '',
            params.option_id || ''
        ].join('|');
    }

    function shouldSend(name, params) {
        var key = dedupeKey(name, params);
        var now = Date.now();

        if (recent[key] && now - recent[key] < 600) {
            return false;
        }

        recent[key] = now;
        return true;
    }

    function sendNow(item) {
        if (!COUNTER_ID || typeof window.ym !== 'function') {
            return false;
        }

        try {
            window.ym(COUNTER_ID, 'reachGoal', item.name, item.params);
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

    function emit(name, extra) {
        var params;

        if (!allowedEvents[name]) {
            return;
        }

        params = buildParams(extra);
        if (!shouldSend(name, params)) {
            return;
        }

        var item = { name: name, params: params };

        if (!sendNow(item)) {
            queue.push(item);
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

        try {
            document.dispatchEvent(new CustomEvent('talario:behavior', {
                detail: { name: name, params: params }
            }));
        } catch (e) {
            // CustomEvent is optional. Analytics delivery above is authoritative.
        }
    }

    $(document).on('submit', 'form[name="search_form"]', function () {
        emit('talario_search_submit');
    });

    $(document).on('change', '.cm-product-filters input, .cm-product-filters select', function () {
        emit('talario_filter_apply', {
            filter_id: filterIdFromElement(this)
        });
    });

    $(document).on('click', '.cm-product-filters a.cm-ajax, .ty-product-filters__reset-button', function () {
        emit('talario_filter_apply', {
            filter_id: filterIdFromElement(this)
        });
    });

    $(document).on('click', 'a[href*="products.view"][href*="product_id="]', function () {
        emit('talario_product_open', {
            product_id: productIdFromElement(this)
        });
    });

    $(document).on('change', 'select[name*="[product_options]"], input[name*="[product_options]"], [data-ca-variation-code]', function () {
        var optionId = '';
        var name = String($(this).attr('name') || '');
        var match = name.match(/product_options\]\[(\d{1,12})\]/);

        if (match) {
            optionId = match[1];
        }

        emit('talario_variant_select', {
            product_id: productIdFromElement(this),
            option_id: optionId
        });
    });

    $(document).on('click focus', '#date-range22, .date-picker-wrapper, .ec_select_slots_list, [id^="ec_timeslots"]', function () {
        if (scheduleSeen) {
            return;
        }
        scheduleSeen = true;
        emit('talario_schedule_open', {
            product_id: productIdFromElement(this)
        });
    });

    $(document).on('change', '.ec_select_slots_list, select[name*="[booking_slot]"]', function () {
        if (!String($(this).val() || '')) {
            return;
        }
        emit('talario_slot_select', {
            product_id: productIdFromElement(this)
        });
    });

    $(document).on('click', 'button[name^="dispatch[checkout.add"], input[name^="dispatch[checkout.add"], [id^="button_cart_"]', function () {
        var productId = productIdFromElement(this);
        var isBooking = $(this).closest('form').find('[name*="[booking_info]"]').length > 0;

        emit(isBooking ? 'talario_booking_cta' : 'talario_add_to_cart', {
            product_id: productId
        });
    });

    $(document).on('click', 'a[href*="checkout.checkout"], button[name^="dispatch[checkout.checkout"], input[name^="dispatch[checkout.checkout"]', function () {
        emit('talario_checkout_start');
    });

    window.addEventListener('popstate', function () {
        emit('talario_back');
    });

}(Tygh, Tygh.$));
