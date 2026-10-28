'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const file = '../../js/addons/lr_design_changes/ux01_search_coordinator.js';
const api = require(file);

test('query and selected native feature values combine in one homepage URL', () => {
  const url = new URL(api.buildUrl('https://talario.ru', '  танцы  ', {'239':['1','2','3'], '322':['77']}));
  assert.equal(url.pathname, '/dev_copy/index.php');
  assert.equal(url.searchParams.get('dispatch'), 'index.index');
  assert.equal(url.searchParams.get('q'), 'танцы');
  assert.equal(url.searchParams.get('search_performed'), 'Y');
  assert.equal(url.searchParams.get('features_hash'), '239-1-2-3_322-77');
});

test('age first then search and search first then age have identical request', () => {
  const first = api.buildUrl('https://talario.ru', 'танцы', {'322':['45'], '239':['3','2']});
  const second = api.buildUrl('https://talario.ru', 'танцы', {'239':['2','3'], '322':['45']});
  assert.equal(first, second);
});

test('empty state returns unfiltered homepage and keeps safe first-party dev_copy scope', () => {
  const url = api.buildUrl('https://talario.ru', '', {});
  assert.equal(url, 'https://talario.ru/dev_copy/index.php?dispatch=index.index');
  assert.doesNotMatch(url, /products\.search/);
});

test('native feature hash restores age/direction selection on refresh and back', () => {
  assert.deepEqual(api.parseHash('322-5-8_239-0-3-7'), {'239':['0','3','7'], '322':['5','8']});
  assert.deepEqual(api.parseHash('malformed_input_32-bad'), {});
});

test('inclusive age range generates native variants in correct order', () => {
  const ages = [{value:'10',label:'0 лет'},{value:'11',label:'1 год'},{value:'12',label:'2 года'},{value:'13',label:'3 года'},{value:'14',label:'4 года'}];
  assert.deepEqual(api.ageValues(ages, 2, 4), ['12','13','14']);
  assert.deepEqual(api.ageValues(ages, 4, 2), []);
  assert.deepEqual(api.ageValues(ages, 0, 0), ['10']);
});

test('removing one applied chip preserves q and other filter groups', () => {
  const groups = api.parseHash('239-1-2_322-19');
  delete groups['239'];
  const u = new URL(api.buildUrl('https://talario.ru','танцы',groups));
  assert.equal(u.searchParams.get('q'),'танцы');
  assert.equal(u.searchParams.get('features_hash'),'322-19');
});

test('only dev_copy homepage loads coordinator; other pages keep native CS-Cart handlers', () => {
  const source=fs.readFileSync(require.resolve(file),'utf8');
  assert.match(source,/\/dev_copy/);
  assert.match(source,/#talario-ux01/);
  assert.match(source,/addEventListener\('submit'/);
  assert.match(source,/stopImmediatePropagation/);
  assert.match(source,/\.cm-lr-homepage-search-filter:checked/);
  assert.doesNotMatch(source,/\.innerHTML\s*=/);
});

test('production cannot install capture listeners without server-rendered dev_copy gate', () => {
  const js=fs.readFileSync(require.resolve(file),'utf8');
  const hook=fs.readFileSync('design/themes/abt__unitheme2/templates/addons/lr_design_changes/hooks/index/scripts.post.tpl','utf8');
  assert.match(hook,/\$config\.current_path\|default:"" == "\/dev_copy"/);
  const homepage=fs.readFileSync('js/addons/lr_design_changes/ux01_homepage.js','utf8');
  assert.match(homepage,/talarioUx01FilterGate = true/);
  assert.match(homepage,/ux01_search_coordinator\.js/);
  assert.match(js,/talarioUx01FilterGate !== true/);
  assert.match(js,/root\.location\.pathname/);
  // JS is loaded from dev_copy only. Even accidental global inclusion has no capture handlers.
  assert.doesNotMatch(js,/root\.TalarioUX01Filters\s*=/);
});

test('quick filters show one human-readable selection, not native checkbox counts', () => {
  assert.deepEqual(api.formatQuickLabel('Возраст', ['2 года', '3 года', '4 года', '5 лет', '6 лет']),
    {short: '2–6 лет', full: 'Возраст: 2–6 лет'});
  assert.deepEqual(api.formatQuickLabel('Возраст', ['2 года', '3 года', '4 года']),
    {short: '2–4 года', full: 'Возраст: 2–4 года'});
  assert.deepEqual(api.formatQuickLabel('Направление', ['Детские лагеря']),
    {short: 'Детские лагеря', full: 'Направление: Детские лагеря'});
  assert.deepEqual(api.formatQuickLabel('Направление', ['Спорт', 'Танцы', 'Творчество', 'Музыка', 'Языки']),
    {short: '5 направлений', full: 'Направления: выбрано 5'});
  assert.deepEqual(api.formatQuickLabel('Направление', []),
    {short: 'Направление', full: 'Направление'});
});

test('native filter-count span is visually suppressed while JS keeps native state', () => {
  const js = fs.readFileSync(require.resolve(file), 'utf8');
  const css = fs.readFileSync('design/themes/abt__unitheme2/css/addons/lr_design_changes/ux01_homepage.less', 'utf8');
  assert.match(js, /ux01-quick-label/);
  assert.match(js, /formatQuickLabel\(base, chosen\)/);
  assert.match(css, /data-ca-lr-homepage-search-filters-count/);
  assert.match(css, /ux01-quick-label/);
  assert.match(css, /text-overflow:ellipsis/);
});


test('mobile consent banner reserves space for bottom-sheet apply without dismissing consent', () => {
  assert.equal(api.cookieReserve({top: 738, bottom: 832, height: 94}, 844, true), 114);
  assert.equal(api.cookieReserve({top: 738, bottom: 832, height: 94}, 844, false), 0);
  assert.equal(api.cookieReserve({top: 900, bottom: 970, height: 70}, 844, true), 0);
  assert.equal(api.cookieReserve({top: 0, bottom: 0, height: 0}, 844, true), 0);
  assert.equal(api.cookieReserve({top: 520, bottom: 640, height: 120}, 844, true), 0);
});

test('mobile consent layout makes bottom sheet stop above Klaro notice, keeps popup action available', () => {
  const js=fs.readFileSync(require.resolve(file),'utf8');
  const css=fs.readFileSync('design/themes/abt__unitheme2/css/addons/lr_design_changes/ux01_homepage.less','utf8');
  assert.match(js,/#klaro \.cookie-notice/);
  assert.match(js,/MutationObserver/);
  assert.match(js,/--ux01-consent-inset/);
  assert.match(css,/--ux01-consent-inset/);
  assert.match(css,/max-height:~?"min\(80dvh, calc\(100dvh/);
});

