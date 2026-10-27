'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
test('geometry QA uses media viewport rather than scrollbar-reduced client width at 900px', () => {
  const sandbox = {};
  vm.runInNewContext(fs.readFileSync('tests/ux01/live-geometry.js', 'utf8'), sandbox);
  for (const [innerWidth, clientWidth, expected] of [[899,891,1], [900,892,2], [901,893,2]]) {
    const outer = { getBoundingClientRect: () => ({x:0,y:0,width:clientWidth,height:0,bottom:0}) };
    const feed = {parentElement:outer, scrollWidth:0, clientWidth:0, querySelectorAll:()=>[], querySelector:()=>null, getBoundingClientRect:()=>({x:0,y:0,width:0,height:0,bottom:0})};
    outer.querySelector = () => null;
    const doc = {location:{origin:'https://talario.ru',pathname:'/dev_copy/'}, defaultView:{innerWidth}, documentElement:{clientWidth,scrollWidth:clientWidth}, querySelector:s=>s==='.talario-ux01 .feed'?feed:null,querySelectorAll:()=>[]};
    assert.equal(sandbox.auditCatalogGeometry(doc).columns, expected, 'media viewport ' + innerWidth);
  }
});
