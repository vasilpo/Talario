'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const cp = require('node:child_process');

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
  assert.match(source, /appendChild\(catalog\)/);
  assert.doesNotMatch(source, /\.innerHTML\s*=/);
  assert.doesNotMatch(source, /\.remove\(/);
});
test('only the first name from escaped server context is rendered as text', () => {
  const js = fs.readFileSync(jsPath, 'utf8');
  const tpl = fs.readFileSync(scriptsPath, 'utf8');
  assert.match(js, /data-first-name/);
  assert.match(js, /textContent\s*=\s*text/);
  assert.doesNotMatch(js, /lastName|lastname|fullName|fullname/);
  assert.match(tpl, /\$user_info\.firstname/);
  assert.match(tpl, /escape:"html"/);
  assert.doesNotMatch(tpl, /\$user_info\.lastname/);
});
test('theme CSS cannot affect non-enhanced pages', () => {
  const css = fs.readFileSync(cssPath, 'utf8');
  assert.match(css, /html\.talario-ux01-active\s*\{/);
  assert.match(css, /@media\s*\(max-width:\s*900px\)/);
  assert.match(css, /@media\s*\(max-width:\s*375px\)/);
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

test('homepage hooks must not depend on an optional index mode or print literal backslash-newline text', () => {
  const script = fs.readFileSync(scriptsPath, 'utf8');
  const style = fs.readFileSync(stylesPath, 'utf8');
  for (const hook of [script, style]) {
    assert.ok(hook.includes('$runtime.controller|default:"" == "index"'));
    assert.doesNotMatch(hook, /\$runtime\.mode/);
    assert.doesNotMatch(hook, /\\n\{if/);
  }
});
