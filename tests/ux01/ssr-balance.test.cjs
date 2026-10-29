'use strict';

const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../../js/addons/lr_design_changes/ux01_homepage.js'), 'utf8');

// Small DOM boundary for the real document-ready/commoninit render path. The
// server header/main exist before the script runs; no JS-created-header seam.
class Element {
  constructor(tag, classes = '', text = '') {
    this.tagName = tag.toUpperCase(); this.nodeType = 1;
    this.attributes = {class: classes}; this.childNodes = []; this.parentNode = null;
    this.style = {setProperty(name, value) { this[name] = value; }};
    this.classList = {
      contains: name => this.className.split(/\s+/).includes(name),
      add: (...names) => { this.className = [...new Set([...this.className.split(/\s+/).filter(Boolean), ...names])].join(' '); },
      remove: (...names) => { this.className = this.className.split(/\s+/).filter(name => !names.includes(name)).join(' '); },
      toggle: (name, enabled) => { this.classList[enabled ? 'add' : 'remove'](name); }
    };
    this.textContent = text;
  }
  get id() { return this.getAttribute('id') || ''; }
  set id(value) { this.setAttribute('id', value); }
  get className() { return this.getAttribute('class') || ''; }
  set className(value) { this.setAttribute('class', value); }
  get href() { return this.getAttribute('href') || ''; }
  set href(value) { this.setAttribute('href', value); }
  get parentElement() { return this.parentNode; }
  get children() { return this.childNodes.filter(node => node.nodeType === 1); }
  get firstChild() { return this.childNodes[0] || null; }
  get textContent() { return this.childNodes.map(node => node.textContent).join(''); }
  set textContent(value) { this.childNodes.forEach(node => { node.parentNode = null; }); this.childNodes = []; if (value) this.appendChild({nodeType:3, textContent:String(value), parentNode:null}); }
  getAttribute(name) { return this.attributes[name] ?? null; }
  setAttribute(name, value) { this.attributes[name] = String(value); }
  removeAttribute(name) { delete this.attributes[name]; }
  appendChild(node) { return this.insertBefore(node, null); }
  insertBefore(node, reference) {
    if (reference !== null && reference.parentNode !== this) throw new DOMException('Reference is not a direct child', 'NotFoundError');
    if (node === reference) return node;
    if (node.parentNode) node.parentNode.removeChild(node);
    this.childNodes.splice(reference === null ? this.childNodes.length : this.childNodes.indexOf(reference), 0, node);
    node.parentNode = this; return node;
  }
  removeChild(node) { this.childNodes.splice(this.childNodes.indexOf(node), 1); node.parentNode = null; return node; }
  remove() { if (this.parentNode) this.parentNode.removeChild(this); }
  addEventListener() {}
  matches(selector) {
    const tag = selector.match(/^[a-z][\w-]*/i);
    if (tag && this.tagName !== tag[0].toUpperCase()) return false;
    for (const [, id] of selector.matchAll(/#([\w-]+)/g)) if (this.id !== id) return false;
    for (const [, cls] of selector.matchAll(/\.([\w-]+)/g)) if (!this.classList.contains(cls)) return false;
    for (const [, key, value] of selector.matchAll(/\[([\w-]+)(?:="([^"]*)")?\]/g)) {
      if (this.getAttribute(key) === null || (value !== undefined && this.getAttribute(key) !== value)) return false;
    }
    return true;
  }
  querySelectorAll(selector) {
    const alternatives = selector.split(',').map(part => part.trim().split(/\s+/));
    const found = [];
    const visit = parent => parent.children.forEach(node => {
      if (alternatives.some(parts => {
        let index = parts.length - 1, ancestor = node;
        if (!ancestor.matches(parts[index--])) return false;
        while (index >= 0) {
          ancestor = ancestor.parentElement;
          while (ancestor && !ancestor.matches(parts[index])) ancestor = ancestor.parentElement;
          if (!ancestor) return false;
          index--;
        }
        return true;
      })) found.push(node);
      visit(node);
    });
    visit(this); return found;
  }
  querySelector(selector) { return this.querySelectorAll(selector)[0] || null; }
}

function serverHomepage({authenticated = true, phase = 'pending'} = {}) {
  const html = new Element('html', 'talario-ux01-boot'), body = new Element('body'); html.appendChild(body);
  const shell = new Element('section', 'talario-ux01'); shell.id = 'talario-ux01';
  shell.setAttribute('data-ux01-server-shell', phase);
  const header = new Element('header', 'header'), actions = new Element('div', 'header-actions');
  const profile = new Element('a', 'iconbutton user-link');
  profile.href = '/dev_copy/' + (authenticated ? 'profiles-update/' : 'login/');
  if (authenticated) profile.appendChild(new Element('span', 'header-user-name', 'Василий'));
  // Observed server-rendered header on 2926894a: auth and guest both receive
  // the direct profile SVG, before balance hydration appends its nested coins.
  profile.appendChild(new Element('svg'));
  actions.appendChild(profile); header.appendChild(actions); shell.appendChild(header);
  const main = new Element('main', 'talario-ux01__main'); main.appendChild(new Element('h1', '', 'Занятия для детей'));
  shell.appendChild(main); body.appendChild(shell);
  const native = new Element('div', 'tygh-content'), content = new Element('div', 'content-grid');
  native.appendChild(content); body.appendChild(native);
  const search = new Element('div', 'ty-search-block'), form = new Element('form'); form.setAttribute('name', 'search_form');
  search.appendChild(form); content.appendChild(search);
  const filters = new Element('div', 'cm-lr-homepage-search-filters'); content.appendChild(filters);
  const catalog = new Element('div'); catalog.id = 'homepage_catalog'; content.appendChild(catalog);
  const document = {
    documentElement: html,
    createElement: tag => new Element(tag), createElementNS: (_, tag) => new Element(tag),
    createTextNode: text => ({nodeType:3, textContent:String(text), parentNode:null}),
    getElementById: id => html.querySelector('#' + id), querySelector: selector => html.querySelector(selector),
    addEventListener() {}
  };
  const ready = [], events = new Map(), requests = [];
  const jquery = callback => { if (typeof callback === 'function') ready.push(callback); };
  jquery.ceEvent = (action, name, callback) => { assert.equal(action, 'on'); events.set(name, callback); };
  const tygh = {$: jquery, talarioUx01Context: {authenticated, firstName: authenticated ? 'Василий' : ''}};
  const window = {
    location: {origin:'https://talario.ru', pathname:'/dev_copy/', search:''}, Tygh: tygh,
    fetch(url, options) {
      const request = {url, options}; requests.push(request);
      return new Promise((resolve, reject) => { request.resolve = resolve; request.reject = reject; });
    }
  };
  vm.runInNewContext(source, {document, window, Tygh:tygh, URLSearchParams}, {filename:'ux01_homepage.js'});
  return {
    shell, header, main, profile, requests,
    startup: () => ready.forEach(callback => callback()),
    commoninit: () => { assert.ok(events.has('ce.commoninit')); events.get('ce.commoninit')(); }
  };
}

const settle = () => new Promise(resolve => setImmediate(resolve));
const directProfileIcons = profile => profile.children.filter(node => node.tagName.toLowerCase() === 'svg');
function expectPrivateRequest(page) {
  assert.equal(page.requests.length, 1, 'the existing server header requests its own balance exactly once');
  assert.equal(page.requests[0].url, '/dev_copy/index.php?dispatch=ux01_loyalty.balance');
  assert.equal(page.requests[0].options.credentials, 'same-origin');
  assert.equal(page.requests[0].options.cache, 'no-store');
}

for (const phase of ['pending', 'ready']) {
  test(`authenticated SSR ${phase} removes the redundant profile avatar while preserving balance coins`, async () => {
    const page = serverHomepage({phase});
    assert.equal(directProfileIcons(page.profile).length, 1, 'fixture reproduces the actual server avatar');
    page.startup(); page.commoninit();
    assert.equal(directProfileIcons(page.profile).length, 0, 'the authenticated header must be name, coins balance and basket, without a profile avatar');
    expectPrivateRequest(page);
    page.requests[0].resolve({ok:true, json:async () => ({authenticated:true, points:-100})});
    await settle();
    const balance = page.profile.querySelector('.ux01-header-balance');
    assert.ok(balance, 'native balance still hydrates into the retained server header');
    assert.equal(balance.textContent, '-100');
    assert.equal(balance.querySelectorAll('svg').length, 1, 'the gold coins inside balance are preserved');
    assert.equal(page.profile.querySelector('.header-user-name').textContent, 'Василий');
    page.commoninit(); await settle();
    assert.equal(directProfileIcons(page.profile).length, 0, 'AJAX commoninit cannot restore the redundant avatar');
    assert.equal(balance.querySelectorAll('svg').length, 1, 'reinit cannot remove nested balance coins');
    assert.equal(page.profile.querySelectorAll('.ux01-header-balance').length, 1);
    expectPrivateRequest(page);
  });
}

for (const phase of ['pending', 'ready']) for (const points of [-100, 0, 1500]) {
  test(`authenticated SSR ${phase} header renders the endpoint's ${points} points once`, async () => {
    const page = serverHomepage({phase});
    page.startup();
    page.commoninit();
    expectPrivateRequest(page);
    page.requests[0].resolve({ok:true, json:async () => ({authenticated:true, points})});
    await settle();
    const balance = page.profile.querySelector('.ux01-header-balance');
    assert.ok(balance, 'the signed native balance, including negative and zero values, is displayed unchanged');
    assert.equal(balance.textContent.replace(/[\s\u00a0\u202f]/g, ''), String(points));
    assert.equal(page.profile.getAttribute('data-ux01-balance-status'), 'ready');
    assert.equal(page.profile.querySelector('.header-user-name').textContent, 'Василий');
    assert.ok(page.shell.querySelector('.header') === page.header, 'server header is retained');
    assert.ok(page.shell.querySelector('.talario-ux01__main') === page.main, 'server main is retained');
    page.commoninit(); await settle();
    expectPrivateRequest(page);
    assert.equal(page.profile.querySelectorAll('.ux01-header-balance').length, 1);
  });
}

test('guest SSR startup and repeated commoninit never request or display a personal balance', async () => {
  const page = serverHomepage({authenticated:false});
  page.startup(); page.commoninit(); await settle();
  assert.equal(page.requests.length, 0);
  assert.equal(page.shell.querySelectorAll('.ux01-header-balance').length, 0);
  assert.equal(directProfileIcons(page.profile).length, 1, 'guest retains the native login icon');
  assert.equal(page.profile.href, '/dev_copy/login/');
});

for (const failure of ['http', 'network', 'unavailable']) {
  test(`SSR balance ${failure} failure does not invent a zero balance`, async () => {
    const page = serverHomepage(); page.startup(); expectPrivateRequest(page);
    if (failure === 'network') page.requests[0].reject(new Error('Network unavailable'));
    else page.requests[0].resolve({ok:failure !== 'http', status:503, json:async () => ({authenticated:true, points:null})});
    await settle();
    assert.equal(page.profile.querySelectorAll('.ux01-header-balance').length, 0);
    assert.ok(['error', 'unavailable'].includes(page.profile.getAttribute('data-ux01-balance-status')));
    assert.equal(page.profile.querySelector('.header-user-name').textContent, 'Василий');
  });
}
