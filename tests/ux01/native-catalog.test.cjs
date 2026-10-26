'use strict';

const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../../js/addons/lr_design_changes/ux01_homepage.js'), 'utf8');

// Small DOM fake for the public startup/commoninit boundary. In particular,
// insertBefore enforces the browser's direct-child contract; a permissive array
// splice here would hide the production NotFoundError this regression covers.
class Element {
  constructor(tagName) {
    this.tagName = tagName.toUpperCase();
    this.nodeType = 1;
    this.attributes = {};
    this.childNodes = [];
    this.parentNode = null;
    this.style = { setProperty(name, value) { this[name] = value; } };
    this.classList = {
      contains: name => this.className.split(/\s+/).includes(name),
      add: (...names) => { this.className = [...new Set([...this.className.split(/\s+/).filter(Boolean), ...names])].join(' '); },
      remove: (...names) => { this.className = this.className.split(/\s+/).filter(name => !names.includes(name)).join(' '); },
      toggle: (name, force) => {
        const active = force === undefined ? !this.classList.contains(name) : Boolean(force);
        this.classList[active ? 'add' : 'remove'](name);
        return active;
      }
    };
  }
  get className() { return this.getAttribute('class') || ''; }
  set className(value) { this.setAttribute('class', value); }
  get id() { return this.getAttribute('id') || ''; }
  set id(value) { this.setAttribute('id', value); }
  get children() { return this.childNodes.filter(node => node.nodeType === 1); }
  get parentElement() { return this.parentNode; }
  get firstChild() { return this.childNodes[0] || null; }
  get nextSibling() {
    return this.parentNode ? this.parentNode.childNodes[this.parentNode.childNodes.indexOf(this) + 1] || null : null;
  }
  get nextElementSibling() {
    return this.parentNode ? this.parentNode.children[this.parentNode.children.indexOf(this) + 1] || null : null;
  }
  get textContent() { return this.childNodes.map(node => node.textContent).join(''); }
  set textContent(value) {
    this.childNodes.forEach(node => { node.parentNode = null; });
    this.childNodes = [];
    if (value !== '') this.appendChild({ nodeType: 3, textContent: String(value), parentNode: null });
  }
  getAttribute(name) { return this.attributes[name] ?? null; }
  setAttribute(name, value) { this.attributes[name] = String(value); }
  appendChild(node) { return this.insertBefore(node, null); }
  insertBefore(node, reference) {
    if (reference !== null && reference.parentNode !== this) {
      throw new DOMException('The node before which the new node is to be inserted is not a child of this node.', 'NotFoundError');
    }
    if (node === reference) return node;
    if (node.parentNode) node.parentNode.removeChild(node);
    const index = reference === null ? this.childNodes.length : this.childNodes.indexOf(reference);
    this.childNodes.splice(index, 0, node);
    node.parentNode = this;
    return node;
  }
  removeChild(node) {
    const index = this.childNodes.indexOf(node);
    if (index < 0) throw new DOMException('The node to be removed is not a child of this node.', 'NotFoundError');
    this.childNodes.splice(index, 1);
    node.parentNode = null;
    return node;
  }
  insertAdjacentElement(position, node) {
    assert.equal(position, 'afterend', 'fixture supports the native title/meta insertion');
    return this.parentNode.insertBefore(node, this.nextSibling);
  }
  addEventListener() {}
  cloneNode(deep = false) {
    const clone = new Element(this.tagName);
    Object.entries(this.attributes).forEach(([name, value]) => clone.setAttribute(name, value));
    if (deep) this.childNodes.forEach(node => clone.appendChild(node.nodeType === 1 ? node.cloneNode(true) : { nodeType: 3, textContent: node.textContent, parentNode: null }));
    return clone;
  }
  matches(selector) {
    const tag = selector.match(/^[a-z][\w-]*/i);
    if (tag && this.tagName !== tag[0].toUpperCase()) return false;
    for (const [, id] of selector.matchAll(/#([\w-]+)/g)) if (this.id !== id) return false;
    for (const [, name] of selector.matchAll(/\.([\w-]+)/g)) if (!this.classList.contains(name)) return false;
    for (const [, name, operator, doubleQuoted, singleQuoted, bare] of selector.matchAll(/\[([\w-]+)(?:([*^$]?=)(?:"([^"]*)"|'([^']*)'|([^\]]*)))?\]/g)) {
      const actual = this.getAttribute(name), expected = doubleQuoted ?? singleQuoted ?? bare;
      if (actual === null) return false;
      if (operator === '=' && actual !== expected) return false;
      if (operator === '*=' && !actual.includes(expected)) return false;
      if (operator === '^=' && !actual.startsWith(expected)) return false;
      if (operator === '$=' && !actual.endsWith(expected)) return false;
    }
    return true;
  }
  closest(selector) {
    for (let node = this; node; node = node.parentElement) if (node.matches(selector)) return node;
    return null;
  }
  querySelectorAll(selector) {
    const alternatives = selector.split(',').map(part => part.trim().split(/\s+/));
    const found = [];
    const visit = parent => {
      parent.children.forEach(node => {
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
    };
    visit(this);
    return found;
  }
  querySelector(selector) { return this.querySelectorAll(selector)[0] || null; }
}

function element(tag, classes = '', text = '') {
  const node = new Element(tag);
  node.className = classes;
  node.textContent = text;
  return node;
}

function nativeProductColumn(productId) {
  // grid_list.tpl's column/item wrappers, plus the active sd_design_changes
  // products:product_multicolumns_list override's form/body/image/content nodes.
  const column = element('div', 'ty-column2');
  const card = element('div', 'ut2-gl__item');
  card.setAttribute('data-product-id', productId);
  const form = element('form');
  const body = element('div', 'ut2-gl__body');
  const image = element('div', 'ut2-gl__image');
  const link = element('a', 'product_icon_lnk');
  const photo = element('img');
  photo.setAttribute('src', '/native-product-' + productId + '.jpg');
  photo.complete = true; photo.naturalWidth = 200; photo.naturalHeight = 200;
  link.appendChild(photo); image.appendChild(link);
  const info = element('div', 'ut2-gl__content');
  info.appendChild(element('a', 'ut2-gl__name product-title', 'Занятие ' + productId));
  body.appendChild(image); body.appendChild(info);
  form.appendChild(body); card.appendChild(form); column.appendChild(card);
  return column;
}

function nativeHomepage(wrapperClass) {
  const html = element('html'), body = element('body'); html.appendChild(body);
  const tyghContent = element('div', 'tygh-content'), content = element('div', 'content-grid');
  body.appendChild(tyghContent); tyghContent.appendChild(content);
  const search = element('div', 'ty-search-block'), searchForm = element('form');
  searchForm.setAttribute('name', 'search_form');
  const input = element('input'); input.setAttribute('type', 'text'); searchForm.appendChild(input);
  const submit = element('button'); submit.setAttribute('type', 'submit'); searchForm.appendChild(submit);
  search.appendChild(searchForm); content.appendChild(search);
  const filters = element('div', 'cm-lr-homepage-search-filters');
  filters.appendChild(element('div', 'ut2-scroll-content')); content.appendChild(filters);
  const ajaxRoot = element('div'); ajaxRoot.id = 'lr_homepage_catalog_42';
  const catalog = element('div'); catalog.id = 'homepage_catalog';
  const grid = element('div', 'grid-list ut2-gl' + (wrapperClass === 'grid-list__load-more' ? ' ut2-load-more-wrap' : ''));
  const columns = element('div', wrapperClass);
  const products = [1, 2, 3, 4].map(nativeProductColumn);
  products.forEach(column => columns.appendChild(column));
  grid.appendChild(columns); catalog.appendChild(grid); ajaxRoot.appendChild(catalog); content.appendChild(ajaxRoot);
  const document = {
    documentElement: html,
    createElement: tag => element(tag),
    createElementNS: (_, tag) => element(tag),
    createTextNode: text => ({ nodeType: 3, textContent: text, parentNode: null }),
    querySelector: selector => html.querySelector(selector),
    getElementById: id => html.querySelector('#' + id),
    addEventListener() {}
  };
  const ready = [], events = new Map();
  const jquery = callback => { if (typeof callback === 'function') ready.push(callback); };
  jquery.ceEvent = (action, name, callback) => {
    assert.equal(action, 'on'); events.set(name, callback);
  };
  vm.runInNewContext(source, {
    document, window: { location: { pathname: '/dev_copy/', search: '' } }, Tygh: { $: jquery }, URLSearchParams
  }, { filename: 'ux01_homepage.js' });
  return {
    document, ajaxRoot, catalog, grid, columns, products, searchForm,
    startup: () => ready.forEach(callback => callback()),
    commoninit: () => { assert.ok(events.has('ce.commoninit')); events.get('ce.commoninit')(); }
  };
}

// Hierarchy confirmed with git show e02d2fe2:
// design/themes/abt__unitheme2/templates/blocks/list_templates/grid_list.tpl
// lines 71, 85, 90, 98: .grid-list > native inner wrapper > .ty-column* > .ut2-gl__item.
// Both non-scroller branches are native output: AJAX block loading uses
// .grid-list__load-more; otherwise the wrapper is .ut2-gl__wrap. Do not flatten
// these wrappers or add .grid-list to them to make the fixture accept a bug.
for (const wrapperClass of ['ut2-gl__wrap', 'grid-list__load-more']) {
  test('native ' + wrapperClass + ' keeps the bonus after the second column through startup, reinit and AJAX append', () => {
    const page = nativeHomepage(wrapperClass);

    assert.doesNotThrow(() => page.startup(), 'cold homepage startup must accept the native nested columns');

    const bonus = page.catalog.querySelector('.mobile-bonus');
    assert.ok(bonus, 'registration bonus is rendered');
    assert.equal(bonus.parentElement, page.columns, 'bonus belongs to the native columns container');
    assert.deepEqual(page.columns.children, [page.products[0], page.products[1], bonus, page.products[2], page.products[3]]);
    assert.equal(page.grid.children[0], page.columns, 'native outer/inner hierarchy is retained');
    assert.equal(page.document.querySelector('#talario-ux01 #lr_homepage_catalog_42'), page.ajaxRoot, 'native AJAX root is retained');
    assert.equal(page.document.querySelector('form[name="search_form"]'), page.searchForm, 'native search form is retained');

    assert.doesNotThrow(() => page.commoninit(), 'native commoninit can run again');
    assert.equal(page.catalog.querySelectorAll('.mobile-bonus').length, 1);
    assert.equal(page.catalog.querySelector('.mobile-bonus'), bonus);

    // Native load-more appends columns into the existing wrapper, then emits
    // ce.commoninit. New cards must be enhanced without moving/duplicating bonus.
    const appended = [5, 6].map(nativeProductColumn);
    appended.forEach(column => page.columns.appendChild(column));
    assert.doesNotThrow(() => page.commoninit(), 'AJAX-appended cards can be enhanced');
    assert.equal(page.catalog.querySelectorAll('.mobile-bonus').length, 1);
    assert.deepEqual(page.columns.children, [page.products[0], page.products[1], bonus, page.products[2], page.products[3], ...appended]);
    assert.equal(page.catalog.querySelectorAll('.ut2-gl__item').length, 6);
    for (const card of page.catalog.querySelectorAll('.ut2-gl__item')) {
      assert.equal(card.getAttribute('data-ux01-ready'), '1', 'every original/appended card is enhanced');
      assert.equal(card.querySelectorAll('.card-top').length, 1, 'reinit does not duplicate card layout');
    }
  });
}
