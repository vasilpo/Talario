const fs = require('fs');
const assert = require('assert');
const { chromium } = require('/Users/vasilpo/.local/lib/node_modules/@playwright/cli/node_modules/playwright');

const root = __dirname + '/../..';
const behavior = fs.readFileSync(root + '/js/addons/talario_analytics/behavior.js', 'utf8');
const jquery = root + '/js/lib/jquery/jquery.min.js';

(async () => {
    const browser = await chromium.launch({
        executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        headless: true,
        args: ['--disable-breakpad']
    });
    const page = await browser.newPage({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });

    await page.setContent(`
        <form id="product_form_1177">
            <input type="hidden" name="product_data[1177][product_id]" value="1177">
            <label><input type="radio" name="feature_552" value="7-10" checked>7-10 лет</label>
            <label><input type="radio" name="feature_553" value="single">Разовое занятие</label>
            <div class="date-picker-wrapper">Дата</div>
            <select name="product_data[1177][booking_info][booking_slot]"><option value="">Выберите время</option><option value="12:30 - 14:00">12:30 - 14:00</option></select>
            <input type="hidden" name="product_data[1177][booking_info][booking_date]" value="17/10/2026">
            <button id="button_cart_1177" name="dispatch[checkout.add..1177]" type="button">Записаться на занятие</button>
        </form>
        <a id="guest-checkout" href="index.php?dispatch=checkout.login_form&return_url=%2Fcheckout%2F">Перейти к оплате</a>
    `);
    await page.addScriptTag({ path: jquery });
    await page.evaluate(() => {
        const handlers = {};
        window.__events = [];
        window.__dataLayer = [];
        window.ym = (id, method, event) => {
            if (method === 'reachGoal') window.__events.push(event);
        };
        window.dataLayerYM = window.__dataLayer;
        window.Tygh = {
            $: window.jQuery,
            yandexMetrika: { settings: { id: 105073425 } },
            current_url: '/products/view'
        };
        window.Tygh.$.ceEvent = (action, name, args) => {
            if (action === 'on') (handlers[name] ||= []).push(args);
            if (action === 'trigger') (handlers[name] || []).forEach(handler => handler.apply(null, args || []));
        };
        window.__triggerCeEvent = (name, args) => window.Tygh.$.ceEvent('trigger', name, args);
        window.__nativeEcommerce = (params, data) => {
            const added = data && data.yandex_metrika && data.yandex_metrika.added;
            if (added && params && params.url && params.url.includes('checkout.add')) {
                window.dataLayerYM.push({ ecommerce: { add: { products: added } } });
            }
        };
        window.dataLayerYM.push({ ecommerce: { detail: { products: [{ id: 1177 }] } } });
        window.Tygh.$.ceEvent('on', 'ce.ajaxdone', (elms, scripts, params, data) => window.__nativeEcommerce(params, data));
        window.talarioAnalyticsContext = { controller: 'products', mode: 'view', product_id: 1177, company_id: 39 };
        document.querySelector('#guest-checkout').addEventListener('click', event => event.preventDefault());
    });
    await page.addScriptTag({ content: behavior });
    await page.waitForTimeout(100);

    await page.locator('input[name="feature_552"]').dispatchEvent('change');
    await page.locator('input[name="feature_553"]').dispatchEvent('change');
    await page.locator('.date-picker-wrapper').click();
    await page.locator('select[name="product_data[1177][booking_info][booking_slot]"]').selectOption('12:30 - 14:00');
    await page.locator('#button_cart_1177').click();
    await page.evaluate(() => window.__triggerCeEvent('ce.ajaxdone', [[], [], { url: 'index.php?dispatch=products.check_slots' }, { yandex_metrika: { added: [{ id: '1177', quantity: '1' }] } }]));
    const beforeSuccessfulAdd = await page.evaluate(() => ({
        addEvents: window.__events.filter(event => event === 'talario_add_to_cart').length,
        ecommerceAdds: window.dataLayerYM.filter(item => item.ecommerce && item.ecommerce.add).length
    }));
    await page.evaluate(() => window.__triggerCeEvent('ce.ajaxdone', [[], [], { url: 'index.php?dispatch=checkout.add..1177' }, { yandex_metrika: { added: [{ id: '1177', quantity: '1' }] } }]));
    await page.locator('#guest-checkout').click();
    await page.waitForTimeout(100);

    const result = await page.evaluate(() => ({
        events: window.__events || [],
        dataLayer: window.dataLayerYM || [],
        ready: document.readyState,
        tygh: Boolean(window.Tygh),
        ymType: typeof window.ym
    }));
    const count = event => result.events.filter(item => item === event).length;
    const counts = {
        talario_product_open: count('talario_product_open'),
        talario_variant_select: count('talario_variant_select'),
        talario_schedule_open: count('talario_schedule_open'),
        talario_slot_select: count('talario_slot_select'),
        talario_booking_cta: count('talario_booking_cta'),
        talario_add_to_cart: count('talario_add_to_cart'),
        talario_checkout_start: count('talario_checkout_start'),
        ecommerce_detail: result.dataLayer.filter(item => item.ecommerce && item.ecommerce.detail).length,
        ecommerce_add: result.dataLayer.filter(item => item.ecommerce && item.ecommerce.add).length
    };
    assert.deepStrictEqual(counts, {
        talario_product_open: 1,
        talario_variant_select: 2,
        talario_schedule_open: 1,
        talario_slot_select: 1,
        talario_booking_cta: 1,
        talario_add_to_cart: 1,
        talario_checkout_start: 1,
        ecommerce_detail: 1,
        ecommerce_add: 1
    });
    assert.deepStrictEqual(beforeSuccessfulAdd, { addEvents: 0, ecommerceAdds: 0 });
    console.log(JSON.stringify(counts, null, 2));
    await browser.close();
})().catch(error => {
    console.error(error.stack || error.message);
    process.exit(1);
});
