/* UX-01 unified search/filter coordinator. Dev-copy homepage only; native CS-Cart filtering remains authoritative. */
(function (root, factory) {
    'use strict';
    var api = factory();
    if (typeof module === 'object' && module.exports) { module.exports = api; }
    if (!root || !root.document) { return; }
    // Never register event capture outside the explicitly enabled dev_copy homepage.
    if (!/^\/dev_copy(?:\/|$)/.test(root.location.pathname) ||
        !root.Tygh || root.Tygh.talarioUx01FilterGate !== true) { return; }
    var boot = function () { api.init(root); };
    if (root.Tygh && root.Tygh.$ && root.Tygh.$.ceEvent) {
        root.Tygh.$.ceEvent('on', 'ce.commoninit', boot);
    }
    if (root.document.readyState === 'loading') {
        root.document.addEventListener('DOMContentLoaded', boot);
    } else { boot(); }
}(typeof window === 'undefined' ? null : window, function () {
    'use strict';
    function ordered(values) {
        return Array.from(new Set((values || []).map(String).filter(function (v) { return /^\d+$/.test(v); })))
            .sort(function (a, b) { return Number(a) - Number(b); });
    }
    function normalize(groups) {
        var normalized = {};
        Object.keys(groups || {}).sort(function (a, b) { return Number(a) - Number(b); })
            .forEach(function (key) {
                if (!/^\d+$/.test(key)) { return; }
                var values = ordered(groups[key]);
                if (values.length) { normalized[key] = values; }
            });
        return normalized;
    }
    function toHash(groups) {
        return Object.keys(normalize(groups)).map(function (id) {
            return id + '-' + normalize(groups)[id].join('-');
        }).join('_');
    }
    function parseHash(hash) {
        var result = {};
        if (!hash || typeof hash !== 'string') { return result; }
        var parts = hash.split('_');
        if (!parts.every(function (item) { return /^\d+(?:-\d+)+$/.test(item); })) { return {}; }
        parts.forEach(function (part) {
            var values = part.split('-'), id = values.shift();
            result[id] = (result[id] || []).concat(values);
        });
        return normalize(result);
    }
    function buildUrl(origin, query, groups) {
        // Server's existing homepage block reads request q/features_hash via fn_get_products.
        // Stay in the reviewed dev_copy storefront rather than creating a second search engine.
        var safeOrigin = typeof window !== 'undefined' && window.location ? window.location.origin : origin;
        var url = new URL('/dev_copy/index.php', safeOrigin);
        url.searchParams.set('dispatch', 'index.index');
        var q = String(query || '').trim();
        var hash = toHash(groups);
        if (q || hash) { url.searchParams.set('search_performed', 'Y'); }
        if (q) { url.searchParams.set('q', q); }
        if (hash) { url.searchParams.set('features_hash', hash); }
        return url.toString();
    }
    // Return the minimum bottom inset required to keep our mobile filter sheet above
    // a *visible* Klaro consent notice. No programmatic dismissal or consent changes.
    function cookieReserve(rect, viewportHeight, visible) {
        if (!visible || !rect || !viewportHeight || rect.height <= 0 ||
            rect.top >= viewportHeight || rect.bottom < viewportHeight * 0.8) { return 0; }
        return Math.min(Math.ceil(viewportHeight - Math.max(0, rect.top) + 8),
            Math.floor(viewportHeight * 0.5));
    }
    function installConsentObserver(root, doc) {
        if (root.innerWidth >= 900) { return; }
        var observer, hostWatcher, observedHost;
        function sync() {
            var notice = doc.querySelector('#klaro .cookie-notice, #klaro .cookie-modal');
            var visible = !!notice && root.getComputedStyle(notice).display !== 'none' &&
                root.getComputedStyle(notice).visibility !== 'hidden' &&
                root.getComputedStyle(notice).pointerEvents !== 'none';
            var rect = visible ? notice.getBoundingClientRect() : null;
            var bottom = cookieReserve(rect, root.innerHeight, visible);
            doc.documentElement.style.setProperty('--ux01-consent-inset', bottom + 'px');
        }
        function observeHost() {
            var host = doc.getElementById('klaro');
            if (!host || host === observedHost || !root.MutationObserver) { return; }
            if (observer) { observer.disconnect(); }
            observedHost = host;
            observer = new root.MutationObserver(sync);
            observer.observe(host, {subtree:true, childList:true, attributes:true,
                attributeFilter:['class', 'style', 'hidden']});
            sync();
        }
        // Klaro can be inserted asynchronously after the storefront is ready.
        if (root.MutationObserver && doc.body) {
            hostWatcher = new root.MutationObserver(function () { observeHost(); });
            hostWatcher.observe(doc.body, {childList:true});
        }
        observeHost();
        root.addEventListener('resize', sync);
        // Each native quick-control popup opens after the click handler runs.
        doc.addEventListener('click', function (event) {
            if (event.target.closest && event.target.closest('[data-ca-lr-homepage-search-filters-switch]')) {
                root.setTimeout(sync, 50);
            }
        });
        sync();
        root.setTimeout(sync, 250);
    }
    function ageNumber(label) {
        var match = String(label || '').match(/(?:^|\s)(\d{1,2})(?:\s|$)/);
        return match ? Number(match[1]) : null;
    }
    function ageValues(variants, min, max) {
        if (!Number.isFinite(min) || !Number.isFinite(max) || min > max) { return []; }
        return variants.filter(function (v) {
            var n = ageNumber(v.label);
            return n !== null && n >= min && n <= max;
        }).map(function (v) { return String(v.value); });
    }
    function elem(doc, tag, cls, text) {
        var el = doc.createElement(tag);
        if (cls) { el.className = cls; }
        if (typeof text === 'string') { el.textContent = text; }
        return el;
    }
    function selectedGroups(filters) {
        var groups = {};
        Array.prototype.forEach.call(filters.querySelectorAll('.cm-lr-homepage-search-filter:checked'), function (input) {
            var id = input.getAttribute('data-ca-filter-id');
            if (!/^\d+$/.test(id || '')) { return; }
            (groups[id] || (groups[id] = [])).push(input.value);
        });
        return normalize(groups);
    }
    function labelFor(input) {
        var li = input.closest('li');
        var label = li && li.querySelector('label');
        return label ? label.textContent.replace(/\s+/g, ' ').trim() : input.value;
    }
    function ageSuffix(age) {
        var lastTwo = age % 100;
        if (lastTwo >= 11 && lastTwo <= 14) { return 'лет'; }
        var last = age % 10;
        return last === 1 ? 'год' : (last >= 2 && last <= 4 ? 'года' : 'лет');
    }
    // The native filter addon already renders a checkbox count. UX-01 shows the
    // human-readable selection instead; never append the native count to the label.
    function formatQuickLabel(base, selected) {
        var chosen = selected || [];
        if (!chosen.length) { return {short:base, full:base}; }
        if (base === 'Возраст') {
            var ages = chosen.map(ageNumber).filter(function (n) { return n !== null; })
                .sort(function (a, b) { return a - b; });
            if (ages.length) {
                var lower = ages[0], upper = ages[ages.length - 1];
                var value = String(lower) + (upper !== lower ? '–' + upper : '') + ' ' + ageSuffix(upper);
                return {short:value, full:'Возраст: ' + value};
            }
            return {short:'Возраст выбран', full:'Возраст выбран'};
        }
        if (base === 'Направление') {
            return chosen.length === 1
                ? {short:chosen[0], full:'Направление: ' + chosen[0]}
                : {short:chosen.length + ' направлений', full:'Направления: выбрано ' + chosen.length};
        }
        return {short:chosen.length === 1 ? chosen[0] : base + ': ' + chosen.length,
            full:base + ': ' + chosen.join(', ')};
    }
    function controls(filters) {
        return Array.prototype.slice.call(filters.querySelectorAll('[data-ca-lr-homepage-search-filters-switch]'));
    }
    function getPopup(doc, control) {
        return doc.getElementById(String(control.id || '').replace(/^sw_/, ''));
    }
    function updateHeader(filters) {
        controls(filters).forEach(function (control) {
            var id = control.getAttribute('data-ca-filter-id');
            var popup = getPopup(filters.ownerDocument, control);
            var checked = popup ? Array.prototype.slice.call(popup.querySelectorAll('.cm-lr-homepage-search-filter:checked')) : [];
            var base = control.getAttribute('data-ux01-base-label') ||
                (/Возраст/i.test(control.textContent) ? 'Возраст' : 'Направление');
            var chosen = checked.map(labelFor);
            var display = formatQuickLabel(base, chosen);
            var label = control.querySelector('.ux01-quick-label');
            if (!label) {
                label = elem(filters.ownerDocument, 'span', 'button-label ux01-quick-label');
                var textNode = Array.prototype.find.call(control.childNodes, function (n) { return n.nodeType === 3; });
                if (textNode) { control.replaceChild(label, textNode); }
                else { control.insertBefore(label, control.firstChild); }
            }
            label.textContent = display.short;
            control.title = display.full;
            control.classList.toggle('selected', chosen.length > 0);
            control.setAttribute('aria-label', display.full + (chosen.length ? ', выбрано' : ''));
            control.setAttribute('data-ux01-selected-count', String(chosen.length));
        });
    }
    function closePopup(popup) {
        var close = popup.querySelector('[data-ca-external-click-id]');
        if (close) { close.click(); return; }
        popup.classList.remove('js_open');
        popup.classList.add('hidden');
    }
    function addAgeRange(doc, popup) {
        if (!popup || popup.querySelector('.ux01-age-range')) { return; }
        var nativeList = popup.querySelector('.ty-product-filters__variants');
        if (!nativeList) { return; }
        var inputs = Array.prototype.slice.call(nativeList.querySelectorAll('.cm-lr-homepage-search-filter'));
        var variants = inputs.map(function (input) { return {value:input.value, label:labelFor(input), input:input}; })
            .filter(function (v) { return ageNumber(v.label) !== null; });
        if (variants.length < 2) { return; }
        variants.sort(function (a, b) { return ageNumber(a.label) - ageNumber(b.label); });
        nativeList.classList.add('ux01-age-native');
        nativeList.setAttribute('aria-hidden', 'true');
        inputs.forEach(function (box) { box.tabIndex = -1; });
        var range = elem(doc, 'div', 'ux01-age-range');
        var title = elem(doc, 'p', 'ux01-age-tip', 'Выбери возрастной диапазон');
        range.appendChild(title);
        var wrap = elem(doc, 'div', 'ux01-age-selects');
        var from = elem(doc, 'select', 'ux01-age-from');
        var to = elem(doc, 'select', 'ux01-age-to');
        [from, to].forEach(function (select) {
            select.appendChild(new Option('Любой', ''));
            variants.forEach(function (v) {
                select.appendChild(new Option(ageNumber(v.label) + ' лет', String(ageNumber(v.label))));
            });
        });
        function field(text, select) {
            var outer = elem(doc, 'label', 'ux01-age-field');
            outer.appendChild(elem(doc, 'span', '', text));
            outer.appendChild(select); return outer;
        }
        from.setAttribute('aria-label', 'Возраст от');
        to.setAttribute('aria-label', 'Возраст до');
        wrap.appendChild(field('От', from)); wrap.appendChild(field('До', to));
        range.appendChild(wrap);
        nativeList.parentElement.insertBefore(range, nativeList);
        var initially = variants.filter(function (v) { return v.input.checked; })
            .map(function (v) { return ageNumber(v.label); });
        if (initially.length) {
            from.value = String(Math.min.apply(null, initially));
            to.value = String(Math.max.apply(null, initially));
        }
        function changed(side) {
            if (!from.value && to.value && side === 'from') { to.value = ''; }
            if (!to.value && from.value && side === 'to') { from.value = ''; }
            if (side === 'from' && from.value && !to.value) { to.value = from.value; }
            if (side === 'to' && to.value && !from.value) { from.value = to.value; }
            if (from.value && to.value && Number(from.value) > Number(to.value)) {
                if (side === 'from') { to.value = from.value; } else { from.value = to.value; }
            }
            var values = from.value && to.value ? ageValues(variants, Number(from.value), Number(to.value)) : [];
            var set = new Set(values);
            variants.forEach(function (v) { v.input.checked = set.has(String(v.value)); });
            // Preserve the native dependent-filter refresh with one event per range selection.
            variants[0].input.dispatchEvent(new Event('change', {bubbles:true}));
        }
        from.addEventListener('change', function () { changed('from'); });
        to.addEventListener('change', function () { changed('to'); });
    }
    function init(root) {
        var doc = root.document;
        if (!/^\/dev_copy(?:\/|$)/.test(root.location.pathname)) { return; }
        var shell = doc.querySelector('#talario-ux01');
        if (!shell || !doc.documentElement.classList.contains('talario-ux01-active')) { return; }
        var filters = shell.querySelector('.cm-lr-homepage-search-filters');
        var form = shell.querySelector('form[name="search_form"]');
        if (!filters || !form || filters.getAttribute('data-ux01-unified') === '1') { return; }
        filters.setAttribute('data-ux01-unified', '1');
        installConsentObserver(root, doc);
        var input = form.querySelector('input[name="q"]') || form.querySelector('input[type="text"]');
        var appliedParams = new URLSearchParams(root.location.search);
        var appliedQ = (appliedParams.get('q') || '').trim();
        var applied = parseHash(appliedParams.get('features_hash') || '');
        if (input && appliedQ && !input.value.trim()) { input.value = appliedQ; }
        // Native filter block does not render checked attributes from features_hash.
        Array.prototype.forEach.call(filters.querySelectorAll('.cm-lr-homepage-search-filter'), function (box) {
            var id = box.getAttribute('data-ca-filter-id');
            if (Object.prototype.hasOwnProperty.call(applied, id || '')) {
                box.checked = applied[id].indexOf(box.value) !== -1;
            }
        });
        // Trigger the native dependent-category loader only once after restoration.
        var changed = filters.querySelector('.cm-lr-homepage-search-filter:checked');
        if (changed) { changed.dispatchEvent(new Event('change', {bubbles:true})); }
        controls(filters).forEach(function (control) {
            var popup = getPopup(doc, control);
            var title = /Возраст/i.test(control.textContent) ? 'Возраст' : 'Направление';
            control.setAttribute('data-ux01-base-label', title);
            if (!popup) { return; }
            Array.prototype.forEach.call(popup.querySelectorAll('.ux01-popup-apply'), function (button) {
                button.parentNode.removeChild(button);
            });
            if (title === 'Возраст') { addAgeRange(doc, popup); }
            var actions = elem(doc, 'div', 'ux01-panel-actions');
            var done = elem(doc, 'button', 'ux01-action-done', 'Готово'); done.type = 'button';
            done.addEventListener('click', function () { closePopup(popup); updateHeader(filters); updatePending(); });
            var mobileApply = elem(doc, 'button', 'ux01-action-mobile', 'Показать занятия'); mobileApply.type = 'button';
            mobileApply.addEventListener('click', function () { applyAll(); });
            actions.appendChild(done); actions.appendChild(mobileApply);
            popup.appendChild(actions);
        });
        var controlsWrap = shell.querySelector('.query-inner');
        var note = elem(doc, 'div', 'ux01-filter-pending');
        note.setAttribute('role', 'status'); note.setAttribute('aria-live', 'polite');
        if (controlsWrap) { controlsWrap.insertAdjacentElement('afterend', note); }
        function permitted() {
            return /^\/dev_copy(?:\/|$)/.test(root.location.pathname) &&
                !!root.Tygh && root.Tygh.talarioUx01FilterGate === true &&
                doc.documentElement.classList.contains('talario-ux01-active') &&
                !!doc.querySelector('#talario-ux01');
        }
        function query() { return input ? String(input.value || '').trim() : ''; }
        function applyAll() {
            var target = buildUrl(root.location.origin, query(), selectedGroups(filters));
            root.location.assign(target);
        }
        function updatePending() {
            updateHeader(filters);
            var isDirty = query() !== appliedQ || toHash(selectedGroups(filters)) !== toHash(applied);
            note.textContent = isDirty ? 'Изменения не применены. Нажми «Найти», чтобы обновить занятия.' : '';
            note.hidden = !isDirty;
            shell.classList.toggle('ux01-has-draft', isDirty);
        }
        // Capture phase prevents older CS-Cart / lr_design_changes delegated handlers
        // from navigating through a different products.search flow.
        form.addEventListener('submit', function (event) {
            if (!permitted()) { return; }
            event.preventDefault(); event.stopPropagation(); event.stopImmediatePropagation(); applyAll();
        }, true);
        filters.addEventListener('click', function (event) {
            if (!permitted() || !event.target.closest('[data-ca-lr-homepage-search-filters-apply]')) { return; }
            event.preventDefault(); event.stopPropagation(); event.stopImmediatePropagation(); applyAll();
        }, true);
        if (input) { input.addEventListener('input', updatePending); }
        filters.addEventListener('change', function () { updatePending(); });
        var chips = elem(doc, 'div', 'ux01-applied-chips wrap');
        chips.setAttribute('aria-label', 'Применённые условия поиска');
        if (controlsWrap) { controlsWrap.insertAdjacentElement('afterend', chips); }
        function makeChip(title, callback) {
            var chip = elem(doc, 'button', 'ux01-chip', title); chip.type = 'button';
            chip.appendChild(elem(doc, 'span', 'ux01-chip-remove', '×'));
            chip.setAttribute('aria-label', 'Убрать: ' + title);
            chip.addEventListener('click', callback); chips.appendChild(chip);
        }
        if (appliedQ) {
            makeChip('Поиск: ' + appliedQ, function () {
                root.location.assign(buildUrl(root.location.origin, '', applied));
            });
        }
        Object.keys(applied).forEach(function (id) {
            var switchEl = filters.querySelector('[data-ca-lr-homepage-search-filters-switch][data-ca-filter-id="' + id + '"]');
            var base = switchEl ? (switchEl.getAttribute('data-ux01-base-label') || 'Фильтр') : 'Фильтр';
            var values = applied[id], nativeLabels = [];
            filters.querySelectorAll('.cm-lr-homepage-search-filter[data-ca-filter-id="' + id + '"]').forEach(function (box) {
                if (values.indexOf(box.value) !== -1) { nativeLabels.push(labelFor(box)); }
            });
            var display = formatQuickLabel(base, nativeLabels).full;
            makeChip(display, function () {
                var next = Object.assign({}, applied); delete next[id];
                root.location.assign(buildUrl(root.location.origin, appliedQ, next));
            });
        });
        if (appliedQ || Object.keys(applied).length) {
            var clear = elem(doc, 'button', 'ux01-clear-all', 'Сбросить всё'); clear.type = 'button';
            clear.addEventListener('click', function () {
                root.location.assign(buildUrl(root.location.origin, '', {}));
            });
            chips.appendChild(clear);
        } else { chips.hidden = true; }
        updatePending();
    }
    return {buildUrl:buildUrl, parseHash:parseHash, toHash:toHash, ageValues:ageValues, formatQuickLabel:formatQuickLabel, cookieReserve:cookieReserve, init:init};
}));
