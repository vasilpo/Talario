'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const cp = require('node:child_process');
const vm = require('node:vm');

const jsPath = 'js/addons/lr_design_changes/ux01_homepage.js';
const cssPath = 'design/themes/abt__unitheme2/css/addons/lr_design_changes/ux01_homepage.less';
const scriptsPath = 'design/themes/abt__unitheme2/templates/addons/lr_design_changes/hooks/index/scripts.post.tpl';
const stylesPath = 'design/themes/abt__unitheme2/templates/addons/lr_design_changes/hooks/index/styles.post.tpl';

test('JavaScript is syntactically valid', () => {
  cp.execFileSync(process.execPath, ['--check', jsPath], { stdio: 'pipe' });
});
test('enhancement is limited to dev_copy', () => {
  const source = fs.readFileSync(jsPath, 'utf8');
  assert.match(source, /\/dev_copy/);
  assert.match(source, /window\.location\.pathname/);
  assert.match(source, /talario-ux01-active/);
});
test('homepage uses existing native CS-Cart search, filter and catalog nodes', () => {
  const source = fs.readFileSync(jsPath, 'utf8');
  assert.match(source, /form\[name="search_form"\]/);
  assert.match(source, /cm-lr-homepage-search-filters/);
  assert.match(source, /#homepage_catalog/);
  assert.match(source, /appendChild\(search\)/);
  assert.match(source, /appendChild\(filters\)/);
  assert.match(source, /appendChild\(ajaxRoot\)/);
  assert.doesNotMatch(source, /\.innerHTML\s*=/);
  assert.doesNotMatch(source, /\b(?:search|catalog|filters|form)\.remove\(/);
});
test('only the first name from escaped server context is rendered as text', () => {
  const js = fs.readFileSync(jsPath, 'utf8');
  const tpl = fs.readFileSync(scriptsPath, 'utf8');
  assert.match(js, /talarioUx01Context/);
  assert.match(js, /textContent\s*=\s*text/);
  assert.doesNotMatch(js, /lastName|lastname|fullName|fullname/);
  assert.match(tpl, /\$user_info\.firstname/);
  assert.match(tpl, /json_encode:15/);
  assert.doesNotMatch(tpl, /\$user_info\.lastname/);
});
test('theme CSS cannot affect non-enhanced pages', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  assert.match(css, /html\.talario-ux01-active/);
  assert.match(css, /@media\s*\(max-width:\s*899px\)/);
  assert.match(css, /@media\s*\(max-width:\s*359px\)/);
  assert.match(css, /text-overflow:\s*ellipsis/);
});
test('hook preserves previously connected native scripts and styles', () => {
  const script = fs.readFileSync(scriptsPath, 'utf8');
  const style = fs.readFileSync(stylesPath, 'utf8');
  assert.match(script, /homepage_catalog\.js/);
  assert.match(script, /homepage_search_filters\.js/);
  assert.match(script, /ux01_homepage\.js/);
  assert.match(style, /addons\/lr_design_changes\/styles\.less/);
  assert.match(style, /ux01_homepage\.less/);
});

test('homepage hooks use the exact dev_copy path instead of mutable route context', () => {
  const script = fs.readFileSync(scriptsPath, 'utf8');
  const style = fs.readFileSync(stylesPath, 'utf8');
  for (const hook of [script, style]) {
    assert.ok(hook.includes('$config.current_path|default:"" == "/dev_copy"'));
    assert.doesNotMatch(hook, /\{if[^}]*\$runtime\.(controller|mode)/);
    assert.doesNotMatch(hook, /\\n\{if/);
  }
});

test('real UniTheme outside-click handler recognizes clicks inside UX-01 quick filters', () => {
  function element(tag = 'div', classes = '') {
    const names = new Set(classes.split(/\s+/).filter(Boolean));
    return {
      tagName: tag, style: {}, attributes: {}, children: [], textContent: '',
      classList: { add: (...items) => items.forEach(x => names.add(x)), contains: x => names.has(x) },
      getAttribute(name) { return this.attributes[name] ?? null; },
      setAttribute(name, value) { this.attributes[name] = value; },
      addEventListener() {},
      appendChild(child) { this.children.push(child); child.parentElement = this; }
    };
  }
  const filters = element(), strip = element();
  const wrapper = element('div', 'ut2__horizontal-product-filters-dropdown');
  const control = element(), label = element('label');
  wrapper.appendChild(control); wrapper.appendChild(label); strip.appendChild(wrapper);
  control.textContent = 'Возраст'; control.id = 'sw_elm_filter_239_322';
  control.firstChild = { nodeType: 3, nodeValue: 'Возраст' };
  filters.querySelector = selector => selector === '.ut2-scroll-content' ? strip : null;
  filters.querySelectorAll = () => [control];
  const document = { createElement: element, createElementNS: (_, tag) => element(tag), getElementById: () => null, addEventListener() {} };
  const jquery = () => {}; jquery.ceEvent = () => {};
  const sandbox = { document, window: { location: { pathname: '/dev_copy/', search: '' } }, Tygh: { $: jquery }, URLSearchParams };
  const source = fs.readFileSync(jsPath, 'utf8').replace('}(Tygh, Tygh.$));', 'globalThis.setupFilters = setupFilters;\n}(Tygh, Tygh.$));');
  vm.runInNewContext(source, sandbox);
  sandbox.setupFilters(filters);

  // Execute the deployed theme handler itself, including its narrow-desktop branch.
  const native = fs.readFileSync('js/addons/abt__unitheme2/abt__ut2.js', 'utf8');
  const match = native.match(/\$\(document\)\.on\('click\.global', (function\(event\) \{[\s\S]*?\n\})\);/);
  assert.ok(match, 'native outside-click handler must be available');
  let forwarded = 0;
  const $ = target => typeof target === 'string' ? { trigger() { forwarded++; } } : {
    closest(selector) {
      for (let node = target; node; node = node.parentElement) {
        if (node.classList.contains(selector.slice(1))) return { length: 1 };
      }
      return { length: 0 };
    }
  };
  const handler = vm.runInNewContext('(' + match[1] + ')', { $, _: { abt__ut2: { device: 'desktop' } }, window: { matchMedia: () => ({ matches: true }) } });
  handler({ target: label });
  assert.equal(forwarded, 0, 'an inside click must not be forwarded as an outside click');
  handler({ target: element() });
  assert.equal(forwarded, 1, 'native outside-click closing remains enabled');
});


/* Regressions reported in owner screenshots on 2026-10-26. */
test('UX-01 desktop catalog uses a centered full-width responsive two-column grid', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  const qa = css.split('/* UX01-VISUAL-QA-2026-10-26 BEGIN */')[1] || '';
  assert.match(qa, /\.feed[^{}]*\{[^}]*width:calc\(100% - 32px\)!important/);
  assert.match(qa, /\.feed[^{}]*\{[^}]*align-items:stretch!important/);
  assert.match(qa, /@media\(min-width:900px\)/);
  assert.match(qa, /grid-template-columns:repeat\(2,minmax\(0,1fr\)\)!important/);
  assert.match(qa, /feed>\[class\*="ty-column"\][^{]*\{[^}]*align-self:stretch!important/);
});

test('UX-01 neutralizes native UniTheme hover absolute positioning and keeps rows aligned', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  const qa = css.split('/* UX01-VISUAL-QA-2026-10-26 BEGIN */')[1] || '';
  assert.match(qa, /activity:hover \.ut2-gl__body/);
  assert.match(qa, /position:static!important/);
  assert.match(qa, /\.ut2-gl__item\.activity[^{}]*\{[^}]*height:100%!important/);
  assert.match(qa, /\.card-top[^{}]*\{[^}]*flex:1/);
});

test('UX-01 filters match search height without boxed segmented grid', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  const qa = css.split('/* UX01-VISUAL-QA-2026-10-26 BEGIN */')[1] || '';
  assert.match(qa, /\.filter-strip[^{}]*\{[^}]*background:transparent!important/);
  assert.match(qa, /\.filter-strip[^{}]*\{[^}]*border:0!important/);
  assert.match(qa, /\.filter-strip[^{}]*\{[^}]*min-height:54px!important/);
  assert.match(qa, /\.search[^{}]*\{[^}]*min-height:54px!important/);
});

test('UX-01 load-more exposes label while hiding idle broken spinner', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  const qa = css.split('/* UX01-VISUAL-QA-2026-10-26 BEGIN */')[1] || '';
  assert.match(qa, /\.ut2-load-more \.loader-wrapper[^{}]*\{[^}]*display:none!important/);
  assert.match(qa, /\.ut2-load-more \.loader-text[^{}]*\{[^}]*display:inline!important/);
  assert.match(qa, /\.ut2-load-more[^{}]*\{[^}]*display:flex!important/);
});


test('UX-01 runtime hover stabilization uses inline important rules on native nodes', () => {
  const js = fs.readFileSync(jsPath, 'utf8');
  assert.match(js, /function stabilizeCard\(card\)/);
  assert.match(js, /setProperty\('position', 'static', 'important'\)/);
  assert.match(js, /setProperty\('height', '100%', 'important'\)/);
  assert.match(js, /setProperty\('transition', 'none', 'important'\)/);
  assert.match(js, /stabilizeCard\(card\)/);
});
test('UX-01 catalog enhancement targets nearest native grid and survives AJAX refresh', () => {
  const js = fs.readFileSync(jsPath, 'utf8');
  assert.match(js, /cards\[0\]\.closest\('\.grid-list'\)/);
  assert.doesNotMatch(js, /cards\[0\]\.parentElement\.parentElement/);
  assert.match(js, /if \(card\.getAttribute\('data-ux01-ready'\) === '1'\)/);
  assert.match(js, /stabilizeCard\(card\)/);
});
test('UX-01 desktop product rows are compact without changing load-more controls', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  const qa = css.split('/* UX01-COMPACT-HOVER-2026-10-26 BEGIN */')[1] || '';
  assert.match(qa, /\.activity \.card-top/);
  assert.match(qa, /grid-template-columns:100px minmax\(0,1fr\)!important/);
  assert.match(qa, /\.activity \.product-title/);
  assert.match(qa, /font-size:\.9375rem!important/);
  assert.match(qa, /\.activity \.address/);
  assert.match(qa, /-webkit-line-clamp:2!important/);
  assert.match(qa, /@media\(min-width:900px\)/);
});

test('UX-01 card photo min-height follows compact body and cannot force old 130px height', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  const qa = css.split('/* UX01-COMPACT-HOVER-2026-10-26 BEGIN */')[1] || '';
  assert.match(qa, /\.ut2-gl__image\.photo-block[^{}]*\{[^}]*min-height:108px!important/);
});
