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
  assert.match(homepage, /if \(!amount \|\| amount\.querySelector\('\.ux01-cashback'\)\)/);
  assert.match(homepage, /amount\.querySelector\('\.ty-price-num'\)/);
  assert.match(homepage, /if \(!soon\) \{ addCardCashback\(amount\); \}/);
  assert.match(homepage, /Вернём /);
  assert.match(homepage, /Кешбэк · 3%/);
  assert.match(homepage, /make\('details', 'ux01-cashback-tip'\)/);
  assert.match(homepage, /\/dev_copy\/oferta-dlya-polzovatelya\//);
});

test('auth flag from server drives guest welcome offer and real balance from auth points', () => {
  assert.match(scripts, /\$config\.current_path\|default:"" == "\/dev_copy"/);
  assert.match(scripts, /authenticated: \{if \$auth\.user_id\|default:0\}true\{else\}false\{\/if\}/);
  assert.match(scripts, /points: \{if \$auth\.user_id\|default:0\}/);
  assert.match(scripts, /\$auth\.points\|default:0\|intval/);
  assert.match(homepage, /userContext && userContext\.authenticated === true && existingWelcome/);
  assert.match(homepage, /existingWelcome\.remove\(\)/);
  assert.match(homepage, /if \(isAuth && ctx\.points !== null/);
  assert.match(homepage, /Number\.isFinite\(Number\(ctx\.points\)\)/);
  assert.match(homepage, /make\('span', 'ux01-header-balance'\)/);
  assert.match(homepage, /dispatch=profiles\.add/);
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
