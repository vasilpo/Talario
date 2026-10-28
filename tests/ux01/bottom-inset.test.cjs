'use strict';

const test = require('node:test');
const assert = require('node:assert/strict');
const api = require('../../js/addons/lr_design_changes/ux01_search_coordinator.js');

// Public DOM boundary fake: selectors distinguish native navigation from Klaro.
// No layout engine, browser, storefront scripts, consent changes, or network.
function viewport({ width = 390, bar = null, notice = null } = {}) {
  const values = new Map([['--ux01-consent-inset', '0px']]);
  const events = new Map();
  const node = (kind, rect, computed = {}) => ({
    kind,
    computed: { display: 'block', visibility: 'visible', pointerEvents: 'auto', ...computed },
    getBoundingClientRect: () => ({ ...rect })
  });
  const nativeBar = bar && node('native', bar.rect, bar.computed);
  const consent = notice && node('notice', notice.rect, notice.computed);
  const klaro = consent && { id: 'klaro' };
  function select(selector) {
    const found = [];
    for (const part of selector.split(',')) {
      let candidate = null;
      if (part.includes('.ut2-sticky-panel__wrap')) candidate = nativeBar;
      else if (part.includes('#klaro') && part.includes('.cookie-notice')) candidate = consent;
      else if (part.includes('#klaro') && !part.includes('.cookie-modal')) candidate = klaro;
      if (candidate && !found.includes(candidate)) found.push(candidate);
    }
    return found;
  }
  const doc = {
    body: {},
    documentElement: {
      style: {
        setProperty: (name, value) => values.set(name, value),
        getPropertyValue: name => values.get(name) || ''
      },
      classList: { contains: name => name === 'talario-ux01-active' || Boolean(nativeBar && name === 'js_sticky-bottom-panel_-exists') }
    },
    querySelector: selector => select(selector)[0] || null,
    querySelectorAll: select,
    getElementById: id => id === 'klaro' ? klaro : null,
    addEventListener() {}
  };
  const root = {
    innerWidth: width,
    innerHeight: 844,
    getComputedStyle: element => element.computed,
    MutationObserver: class { observe() {} disconnect() {} },
    addEventListener(type, callback) {
      if (!events.has(type)) events.set(type, []);
      events.get(type).push(callback);
    },
    setTimeout(callback) { callback(); }
  };
  return {
    install() {
      assert.equal(typeof api.installBottomInsetObserver, 'function', 'bottom inset observer is exported at the DOM boundary');
      api.installBottomInsetObserver(root, doc);
    },
    inset: () => doc.documentElement.style.getPropertyValue('--ux01-consent-inset'),
    resize(width) {
      root.innerWidth = width;
      for (const callback of events.get('resize') || []) callback();
    }
  };
}

test('visible native bottom navigation reserves its occupied viewport area plus an 8px gap', () => {
  const page = viewport({ bar: { rect: { top: 778, bottom: 844, height: 66 } } });
  page.install();
  assert.equal(page.inset(), '74px');
});

test('native navigation above preview reserves both the 48px preview and the 56px native bar', () => {
  const page = viewport({ bar: { rect: { top: 740, bottom: 796, height: 56 } } });
  page.install();
  assert.equal(page.inset(), '112px');
});

test('hidden native navigation contributes no bottom inset', () => {
  for (const computed of [{ display: 'none' }, { visibility: 'hidden' }, { pointerEvents: 'none' }]) {
    const page = viewport({ bar: { rect: { top: 778, bottom: 844, height: 66 }, computed } });
    page.install();
    assert.equal(page.inset(), '0px');
  }
});

test('visible Klaro and native navigation reserve the maximum occupied area rather than adding overlaps', () => {
  const page = viewport({
    bar: { rect: { top: 778, bottom: 844, height: 66 } },
    notice: { rect: { top: 700, bottom: 844, height: 144 } }
  });
  page.install();
  assert.equal(page.inset(), '152px');
});

test('desktop initialization still responds to entering mobile and clears the inset on returning to desktop', () => {
  const page = viewport({ width: 1440, bar: { rect: { top: 778, bottom: 844, height: 66 } } });
  page.install();
  assert.equal(page.inset(), '0px');
  page.resize(390);
  assert.equal(page.inset(), '74px');
  page.resize(1440);
  assert.equal(page.inset(), '0px');
});
