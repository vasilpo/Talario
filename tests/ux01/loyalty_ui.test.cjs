'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const homepage = fs.readFileSync('js/addons/lr_design_changes/ux01_homepage.js', 'utf8');
const coordinator = fs.readFileSync('js/addons/lr_design_changes/ux01_search_coordinator.js', 'utf8');
const scripts = fs.readFileSync('design/themes/abt__unitheme2/templates/addons/lr_design_changes/hooks/index/scripts.post.tpl', 'utf8');
const css = fs.readFileSync('design/themes/abt__unitheme2/css/addons/lr_design_changes/ux01_homepage.less', 'utf8');

function readPureFunction(name) {
  const start = homepage.indexOf('    function ' + name + '(');
  assert.ok(start >= 0, name + ' must exist');
  const end = homepage.indexOf('\n    function ', start + 10);
  assert.ok(end > start, name + ' must be followed by another function');
  return vm.runInNewContext('(' + homepage.slice(start, end).trim() + ')', {}, {timeout: 500});
}

test('cashback is 3% of the actual native displayed price, rounded as reward_points', () => {
  const parseCardPrice = readPureFunction('parseCardPrice');
  const cashbackPoints = readPureFunction('cashbackPoints');
  assert.equal(cashbackPoints(parseCardPrice('1 500')), 45);
  assert.equal(cashbackPoints(parseCardPrice('1\u202f500,00')), 45);
  assert.equal(cashbackPoints(parseCardPrice('1 549,90')), 46);
  assert.equal(cashbackPoints(parseCardPrice('33')), 1);
  assert.equal(cashbackPoints(parseCardPrice('0')), 0);
  assert.equal(cashbackPoints(parseCardPrice('от 1500')), 0);
  assert.equal(cashbackPoints(parseCardPrice('—')), 0);
  assert.equal(cashbackPoints(-100), 0);
});

test('cashback badge is built once per native valid-priced card with meaningful text', () => {
  assert.match(homepage, /if \(!price \|\| !amount \|\| price\.querySelector\('\.ux01-cashback'\)\)/);
  assert.match(homepage, /amount\.querySelector\('\.ty-price-num'\)/);
  assert.match(homepage, /if \(amount && !soon\) \{[\s\S]*?addCardCashback\(price, amount\);/);
  assert.match(homepage, /Вернём /);
  assert.match(homepage, /Вернём 3% от оплаченной суммы/);
  assert.match(homepage, /make\('details', 'ux01-cashback-tip'\)/);
  assert.match(homepage, /\/dev_copy\/oferta-dlya-polzovatelya\//);
});

test('auth-only private endpoint keeps balances out of cached homepage HTML', () => {
  const php = fs.readFileSync('app/addons/lr_design_changes/controllers/frontend/ux01_loyalty.php', 'utf8');
  assert.ok(scripts.includes('authenticated: {if $auth.user_id|default:0}true{else}false{/if}'));
  assert.ok(!scripts.includes('points: {if'));
  assert.ok(php.includes('Cache-Control: private, no-store'));
  assert.ok(php.includes("isset($auth['user_id'])"));
  assert.ok(php.includes('fn_get_user_additional_data(POINTS, $user_id)'));
  assert.ok(php.includes('/dev_copy/'));
  assert.ok(homepage.includes("window.fetch('/dev_copy/index.php?dispatch=ux01_loyalty.balance'"));
  assert.ok(homepage.includes("credentials: 'same-origin', cache: 'no-store'"));
  assert.ok(homepage.includes('data.authenticated !== true'));
  assert.ok(homepage.includes('userContext && userContext.authenticated === true && existingWelcome'));
  assert.ok(homepage.includes('existingWelcome.remove()'));
  assert.ok(homepage.includes('make(\'span\', \'ux01-header-balance\')'));
  assert.ok(homepage.includes('dispatch=profiles.add'));
});

test('desktop and mobile both apply filters to same homepage URL, not close only', () => {
  assert.match(coordinator, /ux01-action-done', 'Показать занятия'/);
  assert.match(coordinator, /done\.addEventListener\('click', function \(\) \{ applyAll\(\); \}\)/);
  assert.match(coordinator, /mobileApply\.addEventListener\('click', function \(\) \{ applyAll\(\); \}\)/);
  assert.match(coordinator, /function applyAll\(\)/);
  assert.match(coordinator, /selectedGroups\(filters\)/);
  assert.doesNotMatch(coordinator, /ux01-action-done', 'Готово'/);
});

test('all CSS remains scoped to enabled UX01, includes keyboard and mobile tooltip support', () => {
  assert.match(css, /UX01-LOYALTY-2026-10-28/);
  assert.match(css, /html\.talario-ux01-active \.talario-ux01 \.ux01-cashback/);
  assert.match(css, /\.ux01-cashback-tip:focus-within>/);
  assert.match(css, /\.ux01-cashback-tip\[open\]>/);
  assert.match(css, /html\.talario-ux01-active\.is-auth \.talario-ux01 \.mobile-bonus/);
  assert.match(css, /@media\(max-width:899px\)/);
});

test('signed-in visitors never receive the desktop welcome offer or guest CTA', () => {
  assert.match(homepage, /if \(!isAuth\) \{[\s\S]*?make\('aside', 'desktop-bonus'\)/);
  assert.match(css, /html\.talario-ux01-active\.is-auth \.talario-ux01 \.desktop-bonus/);
  assert.match(homepage, /userContext && userContext\.authenticated === true && existingWelcome/);
});

test('card cashback is a full-size footer peer beside price, never crammed inside native price span', () => {
  assert.match(homepage, /price\.insertBefore\(badge, more\)/);
  assert.doesNotMatch(homepage, /amount\.appendChild\(badge\)/);
  assert.match(css, /\.ux01-cashback-lines strong\s*\{[^}]*font-size:14px/s);
});

test('account balance endpoint checks dev storefront context and reports failure without inventing points', () => {
  const php = fs.readFileSync('app/addons/lr_design_changes/controllers/frontend/ux01_loyalty.php', 'utf8');
  assert.match(php, /config\.current_path/);
  assert.match(php, /\$_SERVER\['REQUEST_URI'\]/);
  assert.match(php, /!\$is_dev_request/);
  assert.match(php, /auth\['user_id'\]/);
  assert.match(homepage, /data-ux01-balance-status/);
  assert.doesNotMatch(homepage, /ctx\.points/);
});

test('cashback cannot shadow the footer DOM element with a numeric price', () => {
  assert.doesNotMatch(homepage, /var price = nativePrice && parseCardPrice/);
  assert.match(homepage, /var rubles = nativePrice && parseCardPrice/);
  assert.match(homepage, /var points = cashbackPoints\(rubles\)/);
});

test('cashback enhancement exception must not abort rendering of remaining activity cards', () => {
  assert.match(homepage, /if \(amount && !soon\) \{\s*try\s*\{\s*addCardCashback\(price, amount\);\s*\}\s*catch \(error\)/);
  assert.match(homepage, /console\.warn\('UX-01 cashback render failed'/);
});

test('signed in user without a reward points record still sees zero balance', () => {
  const php = fs.readFileSync('app/addons/lr_design_changes/controllers/frontend/ux01_loyalty.php', 'utf8');
  assert.match(php, /if \(empty\(\$raw_points\)\) \{\s*\$raw_points = 0;/);
  assert.match(php, /\$response\['points'\] = \(int\) \$raw_points;/);
  assert.match(homepage, /Number\.isInteger\(data\.points\)/);
  assert.doesNotMatch(homepage, /data\.points < 0/);
});

test('cashback uses the available footer space, with clear desktop and mobile type', () => {
  assert.match(css, /UX01-LOYALTY-PLAIN/);
  assert.match(css, /\.ux01-cashback\s*\{[^}]*width:100%/s);
  assert.match(css, /\.ux01-cashback-lines strong\s*\{[^}]*font-size:14px/s);
  assert.match(css, /@media\(max-width:899px\)\s*\{[\s\S]{0,1900}\.ux01-cashback/);
});

test('cashback sits close to Details on the right without returning to a pill', () => {
  const anchor = css.indexOf('UX01-CASHBACK-RIGHT-20261029');
  assert.ok(anchor >= 0, 'right aligned cashback rule must be present');
  const fix = css.slice(anchor);
  assert.match(fix, /\.card-foot\.ux01-has-cashback \.ux01-cashback\s*\{[\s\S]*?justify-self:end!important/);
  assert.match(fix, /width:max-content!important/);
  assert.match(fix, /max-width:100%/);
  assert.match(fix, /background:transparent!important/);
  assert.match(fix, /border:0!important/);
  assert.match(fix, /@media\s*\(max-width:899px\)/);
});

test('variant 3: the card displays only coins, +N and a help icon, while 3% lives only inside help', () => {
  const start = homepage.indexOf('    function addCardCashback(');
  const end = homepage.indexOf('    function closeFilters(', start);
  const fragment = homepage.slice(start, end);
  assert.ok(start >= 0 && end > start);
  assert.match(fragment, /make\('strong', 'ux01-cashback-value', '\+' \+ points\.toLocaleString\('ru-RU'\)\)/);
  assert.doesNotMatch(fragment, /make\('span', 'ux01-cashback-meta', 'Кешбэк · 3%'\)/);
  assert.doesNotMatch(fragment, /Вернём ' \+ points\.toLocaleString/);
  assert.match(fragment, /make\('details', 'ux01-cashback-tip'\)/);
  assert.match(fragment, /Вернём 3% от оплаченной суммы/);
  assert.match(fragment, /badge\.appendChild\(details\)/);
});

test('variant 3: signed-in header uses name, coins balance and basket without redundant avatar', () => {
  assert.match(homepage, /if \(!isAuth\) \{ profile\.appendChild\(icon\('user'\)\); \}/);
  assert.match(homepage, /profile\.appendChild\(name\)/);
  assert.match(homepage, /make\('span', 'ux01-header-balance'\)/);
  assert.match(homepage, /actions\.appendChild\(cart\)/);
});

test('variant 3: all card footers have stable Details layout, mobile CTA is full width below price and points', () => {
  const anchor = css.indexOf('UX01-VARIANT3-20261029');
  assert.ok(anchor >= 0);
  const rules = css.slice(anchor);
  assert.match(rules, /\.activity \.card-foot\s*\{[\s\S]*?display:grid!important/);
  assert.match(rules, /\.card-foot\s*>\s*a\.more\s*\{[\s\S]*?grid-column:3/);
  assert.match(rules, /\.ux01-cashback\s*\{[\s\S]*?background:transparent!important/);
  assert.match(rules, /@media\s*\(max-width:899px\)/);
  assert.match(rules, /\.card-foot\s*>\s*a\.more\s*\{[\s\S]*?grid-row:2/);
  assert.match(rules, /grid-column:~"1 \/ -1"/);
});
