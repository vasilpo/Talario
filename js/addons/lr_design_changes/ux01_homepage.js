/* UX-01: approved presentation over real CS-Cart nodes, only on dev_copy. */
(function (_, $) {
    'use strict';
    if (!/\/dev_copy(?:\/|$)/.test(window.location.pathname)) { return; }
    var userContext = null;
    // Exact vector paths from the approved Golos Restored source, not replacement icons.
    var icons = {"menu": [["path", {"d": "M4 5h16"}], ["path", {"d": "M4 12h16"}], ["path", {"d": "M4 19h16"}]], "user": [["circle", {"cx": "12", "cy": "8", "r": "5"}], ["path", {"d": "M20 21a8 8 0 0 0-16 0"}]], "cart": [["path", {"d": "m15 11-1 9"}], ["path", {"d": "m19 11-4-7"}], ["path", {"d": "M2 11h20"}], ["path", {"d": "m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4"}], ["path", {"d": "M4.5 15.5h15"}], ["path", {"d": "m5 11 4-7"}], ["path", {"d": "m9 11 1 9"}]], "filter": [["path", {"d": "M10 5H3"}], ["path", {"d": "M12 19H3"}], ["path", {"d": "M14 3v4"}], ["path", {"d": "M16 17v4"}], ["path", {"d": "M21 12h-9"}], ["path", {"d": "M21 19h-5"}], ["path", {"d": "M21 5h-7"}], ["path", {"d": "M8 10v4"}], ["path", {"d": "M8 12H3"}]], "heart": [["path", {"d": "M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"}]], "gift": [["path", {"d": "M12 7v14"}], ["path", {"d": "M20 11v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"}], ["path", {"d": "M7.5 7a1 1 0 0 1 0-5A4.8 8 0 0 1 12 7a4.8 8 0 0 1 4.5-5 1 1 0 0 1 0 5"}], ["rect", {"height": "4", "rx": "1", "width": "18", "x": "3", "y": "7"}]], "search": [["path", {"d": "m21 21-4.34-4.34"}], ["circle", {"cx": "11", "cy": "11", "r": "8"}]]};
    function make(tag, cls, text) {
        var el = document.createElement(tag);
        if (cls) { el.className = cls; }
        if (typeof text === 'string') { el.textContent = text; }
        return el;
    }
    function icon(name) {
        var ns = 'http://www.w3.org/2000/svg';
        var svg = document.createElementNS(ns, 'svg');
        [['viewBox', '0 0 24 24'], ['width', '24'], ['height', '24'], ['fill', 'none'], ['stroke', 'currentColor'], ['stroke-width', '1.8'], ['stroke-linecap', 'round'], ['stroke-linejoin', 'round'], ['aria-hidden', 'true']].forEach(function (a) { svg.setAttribute(a[0], a[1]); });
        (icons[name] || []).forEach(function (item) {
            var child = document.createElementNS(ns, item[0]);
            Object.keys(item[1]).forEach(function (key) { child.setAttribute(key, item[1][key]); });
            svg.appendChild(child);
        });
        return svg;
    }
    function button(text, cls) {
        var el = make('button', cls, text); el.type = 'button'; return el;
    }
    function closeFilters(restoreFocus) {
        document.documentElement.classList.remove('talario-ux01-filters-open');
        var trigger = document.getElementById('talario-ux01-all-filters');
        if (trigger) { trigger.setAttribute('aria-expanded', 'false'); if (restoreFocus) { trigger.focus(); } }
    }
    // Defensive runtime boundary: UniTheme2 native hover uses absolute positioning
    // and dynamic card sizing. CSS-only overrides can lose to per-item inline styles.
    function stabilizeCard(card) {
        if (!card || !card.classList.contains('activity')) { return; }
        var column = card.parentElement;
        var form = card.querySelector('form');
        var body = card.querySelector('.ut2-gl__body');
        [form, body].forEach(function (el) {
            if (!el) { return; }
            el.style.setProperty('position', 'static', 'important');
            el.style.setProperty('inset', 'auto', 'important');
            el.style.setProperty('width', '100%', 'important');
            el.style.setProperty('height', '100%', 'important');
            el.style.setProperty('min-height', '0', 'important');
            el.style.setProperty('transition', 'none', 'important');
            el.style.setProperty('transform', 'none', 'important');
        });
        card.style.setProperty('height', '100%', 'important');
        if (column) {
            column.style.setProperty('min-width', '0', 'important');
            column.style.setProperty('height', '100%', 'important');
        }
    }
    function refreshCatalog() {
        var catalog = document.querySelector('#talario-ux01 #homepage_catalog');
        if (!catalog) { return; }
        var sidebar = catalog.querySelector('.lr-homepage-catalog-layout__sidebar');
        if (sidebar && !sidebar.querySelector('.ux01-drawer-close')) {
            sidebar.id = 'talario-ux01-filter-panel';
            sidebar.setAttribute('aria-label', 'Фильтры занятий');
            var head = make('div', 'ux01-drawer-head');
            head.appendChild(make('h2', '', 'Фильтры'));
            var close = button('Закрыть', 'ux01-drawer-close');
            close.addEventListener('click', function () { closeFilters(true); });
            head.appendChild(close); sidebar.insertBefore(head, sidebar.firstChild);
        }
        var sorting = catalog.querySelector('.ty-sort-container');
        if (sorting && !sorting.querySelector('.ux01-results-title')) {
            var toolbar = make('div', 'catalog-toolbar wrap');
            sorting.parentNode.insertBefore(toolbar, sorting); toolbar.appendChild(sorting);
            sorting.classList.add('resultbar');
            sorting.insertBefore(make('h2', 'ux01-results-title', 'Все занятия'), sorting.firstChild);
            var currentSort = sorting.querySelector('#sw_elm_sort_fields span');
            var sortControl = sorting.querySelector('#sw_elm_sort_fields');
            if (sortControl) { sortControl.closest('.ty-sort-dropdown').classList.add('ux01-sort-dropdown'); }
            if (currentSort && /Популярн/.test(currentSort.textContent)) { currentSort.textContent = 'Популярные'; }
        }
        var cards = catalog.querySelectorAll('.ut2-gl__item');
        var feed = cards.length ? cards[0].closest('.grid-list') : null;
        if (feed) { feed.classList.add('feed', 'wrap'); }
        Array.prototype.forEach.call(cards, function (card) {
            if (card.getAttribute('data-ux01-ready') === '1') { stabilizeCard(card); return; }
            var body = card.querySelector('.ut2-gl__body');
            var image = card.querySelector('.ut2-gl__image');
            var info = card.querySelector('.ut2-gl__content');
            if (!body || !image || !info) { return; }
            card.setAttribute('data-ux01-ready', '1'); card.classList.add('activity');
            var top = make('div', 'card-top');
            body.insertBefore(top, body.firstChild);
            image.classList.add('photo-block'); info.classList.add('activity-info');
            top.appendChild(image); top.appendChild(info);
            var photo = image.querySelector('img');
            if (photo) {
                photo.classList.add('photo');
                photo.addEventListener('load', function () { if (photo.naturalWidth / photo.naturalHeight > 2.3) { photo.style.objectFit = 'contain'; } });
            }
            var title = info.querySelector('.ut2-gl__name');
            var meta = make('div', 'card-meta');
            var age = info.querySelector('.sd-product-info .ut2-gl__bottom');
            if (age && /лет|год|мес/i.test(age.textContent)) { age.classList.add('age'); meta.appendChild(age); }
            var rating = info.querySelector('.ut2-rating-stars');
            if (rating && rating.querySelector('a')) { rating.classList.add('rating'); meta.appendChild(rating); }
            else {
                if (rating) { rating.hidden = true; }
                meta.appendChild(make('span', 'no-reviews', 'Пока нет отзывов'));
            }
            if (title) { title.insertAdjacentElement('afterend', meta); }
            var address = info.querySelector('.sd-product-info');
            if (address) { address.classList.add('address'); }
            var price = info.querySelector('.ut2-gl__price-wrap');
            if (price) {
                price.classList.add('card-foot'); body.appendChild(price);
                var amount = price.querySelector('.ut2-gl__price');
                if (amount) { amount.classList.add('price-stack'); }
                var more = price.querySelector('.ut2-gl__control a');
                if (more) { more.classList.add('more'); }
            } else {
                var soon = Array.prototype.find.call(info.querySelectorAll('*'), function (el) { return el.children.length === 0 && /Скоро можно будет забронировать/.test(el.textContent); });
                if (soon) {
                    soon.textContent = 'Скоро на Таларио'; soon.classList.add('coming-soon-copy');
                    var foot = make('div', 'card-foot'); var stack = make('div', 'price-stack'); stack.appendChild(soon); foot.appendChild(stack);
                    var productLink = info.querySelector('.product-title');
                    if (productLink) { var moreLink = make('a', 'more', 'Подробнее'); moreLink.href = productLink.href; foot.appendChild(moreLink); }
                    body.appendChild(foot);
                }
            }
            var favorite = image.querySelector('.ut2-add-to-wish');
            if (favorite) {
                favorite.classList.add('favorite-toggle'); favorite.setAttribute('role', 'button'); favorite.tabIndex = 0;
                favorite.setAttribute('aria-label', favorite.title || 'В избранное');
                while (favorite.firstChild) { favorite.removeChild(favorite.firstChild); }
                favorite.appendChild(icon('heart'));
                favorite.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); favorite.click(); } });
            }
            var gallery = image.querySelector('.cm-ab-hover-gallery');
            if (gallery && photo) {
                var sources = [photo.getAttribute('src')];
                Array.prototype.forEach.call(gallery.querySelectorAll('[data-ca-product-additional-image-src]'), function (item) {
                    var src = item.getAttribute('data-ca-product-additional-image-src');
                    if (src && sources.indexOf(src) < 0) { sources.push(src); }
                });
                if (sources.length < 2) { gallery.hidden = true; }
                else {
                    var dots = gallery.querySelector('.abt__ut2_hover_gallery_indicators');
                    if (dots) { dots.classList.add('gallery-dots'); Array.prototype.forEach.call(dots.children, function (dot) { dot.classList.add('gallery-dot'); }); }
                    var current = 0; var startX = 0; var startY = 0; var didSwipe = false;
                    image.addEventListener('touchstart', function (e) { startX = e.changedTouches[0].clientX; startY = e.changedTouches[0].clientY; didSwipe = false; }, {passive: true});
                    image.addEventListener('touchend', function (e) {
                        var dx = e.changedTouches[0].clientX - startX; var dy = e.changedTouches[0].clientY - startY;
                        if (Math.abs(dx) < 35 || Math.abs(dx) <= Math.abs(dy)) { return; }
                        current = (current + (dx < 0 ? 1 : -1) + sources.length) % sources.length;
                        photo.src = sources[current]; didSwipe = true;
                        if (dots) { Array.prototype.forEach.call(dots.children, function (dot, i) { dot.classList.toggle('active', i === current); }); }
                    }, {passive: true});
                    image.querySelector('a.product_icon_lnk').addEventListener('click', function (e) { if (didSwipe) { e.preventDefault(); didSwipe = false; } });
                }
            }
            stabilizeCard(card);
            if (photo) {
                var candidates = sources || [photo.getAttribute('src')]; var nextImage = 1;
                var recoverPhoto = function () {
                    if (nextImage < candidates.length) { photo.src = candidates[nextImage++]; return; }
                    photo.hidden = true; if (gallery) { gallery.hidden = true; }
                    if (!image.querySelector('.ux01-no-image')) { image.querySelector('a.product_icon_lnk').appendChild(make('span', 'ux01-no-image', 'Фото отсутствует')); }
                };
                photo.addEventListener('error', recoverPhoto);
                if (photo.complete && photo.naturalWidth === 0) { recoverPhoto(); }
            }
        });
        if (feed && cards.length > 1 && !feed.querySelector('.mobile-bonus')) {
            var bonus = make('aside', 'mobile-bonus'); bonus.appendChild(icon('gift'));
            var copy = make('div'); copy.appendChild(make('strong', '', '500 баллов за регистрацию')); copy.appendChild(make('p', '', '1 балл = 1 ₽')); bonus.appendChild(copy);
            feed.insertBefore(bonus, cards[1].parentElement.nextSibling);
        }
    }
    function setupFilters(filters) {
        if (!filters || filters.getAttribute('data-ux01-ready') === '1') { return; }
        filters.setAttribute('data-ux01-ready', '1');
        var strip = filters.querySelector('.ut2-scroll-content');
        if (!strip) { return; }
        strip.classList.add('filter-strip');
        var apply = filters.querySelector('[data-ca-lr-homepage-search-filters-apply]');
        Array.prototype.forEach.call(filters.querySelectorAll('[data-ca-lr-homepage-search-filters-switch]'), function (control) {
            var text = control.textContent.trim(); var wrap = control.parentElement;
            if (/Возраст/.test(text) || /Категор/.test(text)) {
                // UniTheme's narrow-desktop outside-click handler requires this ancestor.
                wrap.classList.add('ux01-quick-control', 'ut2-horizontal-product-filters-dropdown');
                wrap.style.order = /Возраст/.test(text) ? '1' : '2';
                control.classList.add('quick-button'); control.setAttribute('role', 'button'); control.tabIndex = 0; control.setAttribute('aria-haspopup', 'dialog');
                if (/Категор/.test(text) && control.firstChild.nodeType === 3) { control.firstChild.nodeValue = 'Направление'; }
                control.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); control.click(); } });
                var popup = document.getElementById(control.id.replace(/^sw_/, ''));
                if (popup && apply) { var popupApply = apply.cloneNode(true); popupApply.classList.add('ux01-popup-apply'); popup.appendChild(popupApply); }
            } else { wrap.classList.add('ux01-extra-quick'); }
        });
        var more = button('', 'allfilters'); more.id = 'talario-ux01-all-filters'; more.style.order = '3';
        more.setAttribute('aria-label', 'Фильтры'); more.setAttribute('aria-expanded', 'false'); more.setAttribute('aria-controls', 'talario-ux01-filter-panel');
        more.appendChild(icon('filter')); more.appendChild(make('span', 'allfilters-label', 'Фильтры'));
        var count = make('span', 'filter-count'); var hash = new URLSearchParams(window.location.search).get('features_hash') || '';
        var activeCount = hash.split('_').filter(Boolean).length;
        if (activeCount) { count.textContent = String(activeCount); more.classList.add('has-filters'); more.setAttribute('aria-label', 'Фильтры, активно: ' + activeCount); }
        more.appendChild(count);
        more.addEventListener('click', function () {
            var panel = document.getElementById('talario-ux01-filter-panel');
            if (!panel) { return; }
            var open = !document.documentElement.classList.contains('talario-ux01-filters-open');
            document.documentElement.classList.toggle('talario-ux01-filters-open', open); more.setAttribute('aria-expanded', String(open));
            if (open) { panel.querySelector('.ux01-drawer-close').focus(); }
        });
        strip.appendChild(more);
    }
    function init() {
        var existing = document.getElementById('talario-ux01');
        if (existing) { refreshCatalog(); return; }
        var content = document.querySelector('.tygh-content .content-grid');
        var catalog = document.querySelector('#homepage_catalog');
        var search = content && content.querySelector('.ty-search-block');
        var filters = document.querySelector('.cm-lr-homepage-search-filters');
        if (!content || !catalog || !search || !filters) { return; }
        var form = search.querySelector('form[name="search_form"]'); if (!form) { return; }
        var root = '/dev_copy/'; function href(slug) { return root + slug; }
        var shell = make('section', 'talario-ux01'); shell.id = 'talario-ux01';
        var header = make('header', 'header'); var inner = make('div', 'header-inner wrap');
        var menu = make('details', 'talario-ux01__menu menu'); var summary = make('summary', 'iconbutton'); summary.setAttribute('aria-label', 'Меню'); summary.appendChild(icon('menu')); menu.appendChild(summary);
        var mobileNav = make('nav', 'talario-ux01__menu-panel'); mobileNav.setAttribute('aria-label', 'Меню Таларио');
        [['О нас', 'o-nas/'], ['Блог', 'blog/'], ['Стать партнёром', 'stat-partnerom/']].forEach(function (item) { var a = make('a', '', item[0]); a.href = href(item[1]); mobileNav.appendChild(a); });
        menu.appendChild(mobileNav); inner.appendChild(menu);
        var brand = make('a', 'brand'); brand.href = root; brand.setAttribute('aria-label', 'Таларио — главная');
        var logo = make('img', 'logo'); logo.src = href('design/themes/abt__unitheme2/media/images/talario-ux01-logo.png'); logo.alt = 'Таларио'; logo.width = 140; logo.height = 32; brand.appendChild(logo); inner.appendChild(brand);
        inner.appendChild(make('span', 'header-city', 'Красногорск'));
        var links = mobileNav.cloneNode(true); links.className = 'header-links'; inner.appendChild(links);
        var actions = make('div', 'header-actions'); var ctx = userContext || _.talarioUx01Context || {}; var isAuth = ctx.authenticated === true;
        userContext = ctx; delete _.talarioUx01Context;
        var firstName = isAuth && typeof ctx.firstName === 'string' ? ctx.firstName.trim() : '';
        document.documentElement.classList.toggle('is-auth', isAuth);
        var profile = make('a', 'iconbutton user-link'); profile.href = isAuth ? href('profiles-update/') : href('login/'); profile.setAttribute('aria-label', isAuth ? 'Личный кабинет' : 'Войти');
        if (firstName) { var name = make('span', 'header-user-name', firstName); name.title = firstName; profile.appendChild(name); }
        profile.appendChild(icon('user')); actions.appendChild(profile);
        var cart = make('a', 'iconbutton basket'); cart.href = href('cart/'); cart.setAttribute('aria-label', 'Корзина'); cart.appendChild(icon('cart')); actions.appendChild(cart); inner.appendChild(actions); header.appendChild(inner); shell.appendChild(header);
        var main = make('main', 'talario-ux01__main'); var intro = make('div', 'intro wrap'); var headline = make('div', 'intro-copy');
        var title = make('h1', '', 'Занятия для детей'); title.appendChild(make('br', 'mobile-break')); title.appendChild(document.createTextNode(' '));
        var accent = make('span', 'city-accent'); var rule = make('span', 'city-accent-rule'); rule.setAttribute('aria-hidden', 'true'); accent.appendChild(rule); accent.appendChild(make('span', 'city-accent-text', 'в Красногорске')); title.appendChild(accent);
        headline.appendChild(title); headline.appendChild(make('p', 'subtitle', 'Найди своё — рядом с домом.')); intro.appendChild(headline);
        var bonus = make('aside', 'desktop-bonus'); bonus.appendChild(icon('gift')); var bonusCopy = make('div'); bonusCopy.appendChild(make('strong', '', '500 баллов за регистрацию')); bonusCopy.appendChild(make('p', '', '1 балл = 1 ₽')); bonus.appendChild(bonusCopy); intro.appendChild(bonus); main.appendChild(intro);
        var controls = make('div', 'query-inner wrap'); var searchWrap = make('div', 'talario-ux01__search'); form.classList.add('search');
        var input = form.querySelector('input[type="text"], input[type="search"]'); if (input) { input.placeholder = 'Занятие или центр'; input.setAttribute('aria-label', 'Название занятия или центра'); }
        form.insertBefore(icon('search'), form.firstChild); var submit = form.querySelector('button[type="submit"]'); if (submit) { submit.textContent = 'Найти'; submit.classList.add('search-submit'); submit.setAttribute('aria-label', 'Найти'); }
        searchWrap.appendChild(search); controls.appendChild(searchWrap);
        var filterWrap = make('div', 'quick'); filterWrap.appendChild(filters); controls.appendChild(filterWrap); main.appendChild(controls);
        var catalogWrap = make('div', 'talario-ux01__catalog');
        // Keep the native AJAX root, not only its inner catalog; filters/load-more target it.
        var ajaxRoot = catalog.parentElement;
        if (ajaxRoot && /^lr_homepage_catalog_\d+$/.test(ajaxRoot.id)) { catalogWrap.appendChild(ajaxRoot); }
        else { catalogWrap.appendChild(catalog); }
        main.appendChild(catalogWrap); shell.appendChild(main);
        var tail = document.getElementById('talario-ux01-approved-tail'); if (tail && tail.content) { shell.appendChild(tail.content.cloneNode(true)); }
        var backdrop = button('', 'ux01-filter-backdrop'); backdrop.setAttribute('aria-label', 'Закрыть фильтры'); backdrop.addEventListener('click', function () { closeFilters(true); }); shell.appendChild(backdrop);
        content.insertBefore(shell, content.firstChild); document.documentElement.classList.add('talario-ux01-active');
        setupFilters(filters); refreshCatalog();
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeFilters(true); }
        if (e.key !== 'Tab' || !document.documentElement.classList.contains('talario-ux01-filters-open')) { return; }
        var panel = document.getElementById('talario-ux01-filter-panel');
        var targets = panel && Array.prototype.filter.call(panel.querySelectorAll('button,a[href],input,select,[tabindex="0"]'), function (el) { return !el.disabled && el.getClientRects().length; });
        if (!targets || !targets.length) { return; }
        var first = targets[0]; var last = targets[targets.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });
    $.ceEvent('on', 'ce.commoninit', function () { init(); }); $(init);
}(Tygh, Tygh.$));
