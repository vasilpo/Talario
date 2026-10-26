/* Talario UX-01. Progressive enhancement of the real CS-Cart homepage, dev_copy only.
 * Keep native search, filters, product cards, pagination and cart handlers intact.
 */
(function (_, $) {
    'use strict';
    var path = window.location.pathname;
    if (!/\/dev_copy(?:\/|$)/.test(path)) { return; }

    function make(tag, className, text) {
        var el = document.createElement(tag);
        if (className) { el.className = className; }
        if (typeof text === 'string') { el.textContent = text; }
        return el;
    }

    function init() {
        if (document.documentElement.classList.contains('talario-ux01-active')) { return; }
        var content = document.querySelector('.tygh-content .content-grid');
        var catalog = document.querySelector('#homepage_catalog');
        var search = document.querySelector('.ty-search-block');
        var filters = document.querySelector('.cm-lr-homepage-search-filters');
        var logo = document.querySelector('.tygh-header .ty-logo-container__image');
        if (!content || !catalog || !search || !filters || !logo || !logo.getAttribute('src')) { return; }
        var form = search.querySelector('form[name="search_form"]');
        if (!form) { return; }

        /* The actual storefront URLs are formed from the live search form action. */
        var root = (form.getAttribute('action') || '').replace(/(?:index\.php.*)?$/, '');
        if (!/\/dev_copy\//.test(root)) { root = '/dev_copy/'; }
        function href(slug) { return root.replace(/\/?$/, '/') + slug; }

        var shell = make('section', 'talario-ux01');
        shell.id = 'talario-ux01';
        var header = make('header', 'talario-ux01__header');
        var headerInner = make('div', 'talario-ux01__header-inner');
        var navDetails = make('details', 'talario-ux01__menu');
        var navSummary = make('summary', 'talario-ux01__hamburger', '☰');
        navSummary.setAttribute('aria-label', 'Открыть меню');
        navDetails.appendChild(navSummary);
        var menuPanel = make('nav', 'talario-ux01__menu-panel');
        menuPanel.setAttribute('aria-label', 'Навигация по Таларио');
        [['Все занятия', '#talario-ux01-catalog'], ['О проекте', href('o-nas/')], ['Блог', href('blog/')], ['Стать партнёром', href('stat-partnerom/')]].forEach(function (item) {
            var a = make('a', '', item[0]); a.href = item[1]; menuPanel.appendChild(a);
        });
        navDetails.appendChild(menuPanel);
        headerInner.appendChild(navDetails);

        var brand = make('a', 'talario-ux01__brand'); brand.href = root;
        var brandImg = make('img', 'talario-ux01__logo');
        brandImg.src = logo.src; brandImg.alt = 'Таларио';
        brandImg.width = 141; brandImg.height = 33;
        brand.appendChild(brandImg); headerInner.appendChild(brand);

        var city = make('span', 'talario-ux01__city', '⌖ Красногорск');
        headerInner.appendChild(city);
        var headerLinks = make('nav', 'talario-ux01__header-links');
        [['О нас', href('o-nas/')], ['Блог', href('blog/')], ['Стать партнёром', href('stat-partnerom/')]].forEach(function (item) {
            var a = make('a', '', item[0]); a.href = item[1]; headerLinks.appendChild(a);
        });
        headerInner.appendChild(headerLinks);

        var actions = make('div', 'talario-ux01__actions');
        var ctx = document.getElementById('talario-ux01-user-context');
        var isAuth = !!(ctx && ctx.getAttribute('data-auth') === '1');
        var firstName = isAuth ? (ctx.getAttribute('data-first-name') || '').trim() : '';
        var profile = make('a', 'talario-ux01__profile');
        profile.href = isAuth ? href('index.php?dispatch=profiles.update') : href('login/');
        profile.setAttribute('aria-label', isAuth ? 'Личный кабинет' : 'Войти');
        if (firstName) {
            var userName = make('span', 'talario-ux01__first-name', firstName);
            userName.title = firstName;
            profile.appendChild(userName);
        }
        var person = make('span', 'talario-ux01__user-icon', '♙');
        person.setAttribute('aria-hidden', 'true'); profile.appendChild(person);
        actions.appendChild(profile);
        var cart = make('a', 'talario-ux01__cart', '♧');
        cart.href = href('index.php?dispatch=checkout.cart');
        cart.setAttribute('aria-label', 'Корзина');
        actions.appendChild(cart);
        headerInner.appendChild(actions); header.appendChild(headerInner);
        shell.appendChild(header);

        var main = make('main', 'talario-ux01__main');
        var intro = make('div', 'talario-ux01__intro');
        var headline = make('div', 'talario-ux01__headline');
        var title = make('h1', 'talario-ux01__title');
        title.appendChild(document.createTextNode('Занятия для детей '));
        var accent = make('span', 'talario-ux01__city-accent', 'в Красногорске');
        title.appendChild(accent);
        headline.appendChild(title);
        headline.appendChild(make('p', 'talario-ux01__subtitle', 'Найди своё — рядом с домом.'));
        intro.appendChild(headline);
        var bonus = make('a', 'talario-ux01__bonus');
        bonus.href = href('profiles-add/');
        bonus.appendChild(make('strong', '', '♢  500 баллов за регистрацию'));
        bonus.appendChild(make('span', '', '1 балл = 1 ₽'));
        intro.appendChild(bonus);
        main.appendChild(intro);

        var controlRow = make('div', 'talario-ux01__controls');
        var searchWrap = make('div', 'talario-ux01__search');
        searchWrap.appendChild(search); // Preserve original CS-Cart search listeners and form.
        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.setAttribute('aria-label', 'Найти занятие'); }
        controlRow.appendChild(searchWrap);
        var filtersWrap = make('div', 'talario-ux01__filters');
        filtersWrap.appendChild(filters); // Native CS-Cart filter controls; no reimplementation.
        controlRow.appendChild(filtersWrap);
        main.appendChild(controlRow);

        var catalogWrap = make('div', 'talario-ux01__catalog');
        catalogWrap.id = 'talario-ux01-catalog';
        catalogWrap.appendChild(catalog); // Keeps real items, pagination, prices, filters, cart.
        main.appendChild(catalogWrap);
        shell.appendChild(main);
        content.insertBefore(shell, content.firstChild);
        document.documentElement.classList.add('talario-ux01-active');
    }

    $.ceEvent('on', 'ce.commoninit', function () { init(); });
    $(init);
}(Tygh, Tygh.$));
