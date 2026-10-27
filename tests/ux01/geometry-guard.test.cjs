'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const context = {};
vm.runInNewContext(fs.readFileSync(path.join(__dirname, 'live-geometry.js'), 'utf8'), context);

test('live geometry refuses PROD, missing frames and lookalike paths before reading DOM', () => {
  const documents = [null, {}, ...['/', '/index.php', '/dev_copy_other/', '/dev_copy/straw.php'].map(pathname => ({
    location: {origin: 'https://talario.ru', pathname},
    querySelector() { throw Error('must not inspect excluded document'); }
  })), {location: {origin: 'https://example.com', pathname: '/dev_copy/'}, querySelector() { throw Error('must not inspect another origin'); }}];
  for (const document of documents) {
    const result = context.auditCatalogGeometry(document);
    assert.equal(result.pass, false);
    assert.match(result.errors[0], /refuses documents outside the dev_copy catalog/);
  }
});
