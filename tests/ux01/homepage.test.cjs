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
