'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

test('first-paint guard releases native content even when the compiled bundle never runs', () => {
  const template = fs.readFileSync('design/themes/abt__unitheme2/templates/addons/lr_design_changes/hooks/index/head_scripts.pre.tpl', 'utf8');
  const script = template.match(/\{literal\}([\s\S]*?)\{\/literal\}/)[1];
  const classes = new Set();
  let release;
  const document = { documentElement: { classList: { add: value => classes.add(value), remove: value => classes.delete(value) } } };
  vm.runInNewContext(script, { document, window: { setTimeout(callback, delay) { assert.ok(delay > 0 && delay <= 8000); release = callback; } } });
  assert.ok(classes.has('talario-ux01-boot'), 'legacy content is guarded before body parsing');
  assert.equal(typeof release, 'function', 'release does not depend on Tygh or jQuery');
  release();
  assert.equal(classes.has('talario-ux01-boot'), false, 'a failed bundle cannot leave the catalog hidden indefinitely');
});
