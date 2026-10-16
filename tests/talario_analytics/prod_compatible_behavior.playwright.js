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
        <div id="product_detail_page">
            <form id="product_form_1177" name="product_form_1177">
                <input type="hidden" name="product_data[1177][product_id]" value="1177">
                <label><input type="radio" name="feature_552" value="1357">7–10 лет</label>
                <label><input type="radio" name="feature_553" value="1210">Разовое занятие</label>
                <div class="date-picker-wrapper">Дата</div>
                <select name="product_data[1177][booking_info][booking_slot]"><option value="">Выберите время</option><option value="12:30 - 14:00">12:30 - 14:00</option></select>
                <input type="hidden" name="product_data[1177][booking_info][booking_date]" value="17/10/2026">
                <button id="button_cart_1177" name="dispatch[checkout.add..1177]" type="button">Записаться</button>
            </form>
        </div>
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
            yandexMetrika: { provider: { id: 'default' }, settings: { id: 105073425 } },
            current_url: '/products/view'
        };
        window.Tygh.$.ceEvent = (action, name, args) => {
            if (action === 'on') (handlers[name] ||= []).push(args);
            if (action === 'trigger') (handlers[name] || []).forEach(handler => handler.apply(null, args || []));
        };
        window.__triggerCeEvent = (name, args) => window.Tygh.$.ceEvent('trigger', name, args);
        document.querySelector('#guest-checkout').addEventListener('click', event => event.preventDefault());
    });
    await page.addScriptTag({ content: behavior });
    await page.waitForTimeout(100);

    await page.locator('input[name="feature_552"]').dispatchEvent('change');
    await page.locator('input[name="feature_553"]').dispatchEvent('change');
    await page.locator('.date-picker-wrapper').click();
    await page.locator('select[name="product_data[1177][booking_info][booking_slot]"]').selectOption('12:30 - 14:00');
    await page.locator('#button_cart_1177').click();
    await page.evaluate(() => window.__triggerCeEvent('ce.ajaxdone', [[], [], { url: 'index.php?dispatch=products.check_slots' }, { yandex_metrika: { added: [{ id: '1177' }] } }]));

    const beforeSuccessfulAdd = await page.evaluate(() => window.__events.filter(event => event === 'talario_add_to_cart').length);
    assert.strictEqual(beforeSuccessfulAdd, 0);

    await page.evaluate(() => window.__triggerCeEvent('ce.ajaxdone', [[], [], { url: 'index.php?dispatch=checkout.add..1177' }, { yandex_metrika: { added: [{ id: '1177', quantity: '1' }] } }]));
    await page.locator('#guest-checkout').click();
    await page.waitForTimeout(100);

    const result = await page.evaluate(() => ({
        events: window.__events || []
    }));
    const count = event => result.events.filter(item => item === event).length;
    const counts = {
        talario_product_open: count('talario_product_open'),
        talario_variant_select: count('talario_variant_select'),
        talario_schedule_open: count('talario_schedule_open'),
        talario_slot_select: count('talario_slot_select'),
        talario_booking_cta: count('talario_booking_cta'),
        talario_add_to_cart: count('talario_add_to_cart'),
        talario_checkout_start: count('talario_checkout_start')
    };

    assert.deepStrictEqual(counts, {
        talario_product_open: 1,
        talario_variant_select: 2,
        talario_schedule_open: 1,
        talario_slot_select: 1,
        talario_booking_cta: 1,
        talario_add_to_cart: 1,
        talario_checkout_start: 1
    });
    console.log(JSON.stringify(counts, null, 2));
    await browser.close();
})().catch(error => {
    console.error(error.stack || error.message);
    process.exit(1);
});
