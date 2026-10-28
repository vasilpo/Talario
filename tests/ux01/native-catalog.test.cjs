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
    this.listeners = new Map();
    this.hidden = false;
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
  get href() { return this.getAttribute('href') || ''; }
  set href(value) { this.setAttribute('href', value); }
  get src() { return this.getAttribute('src') || ''; }
  set src(value) { this.setAttribute('src', value); }
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
    if (value !== '') this.appendChild(textNode(value));
  }
  getAttribute(name) { return this.attributes[name] ?? null; }
  setAttribute(name, value) { this.attributes[name] = String(value); }
  removeAttribute(name) { delete this.attributes[name]; }
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
  addEventListener(type, callback) {
    if (!this.listeners.has(type)) this.listeners.set(type, []);
    this.listeners.get(type).push(callback);
  }
  dispatchEvent(event) {
    event.target = this;
    event.currentTarget = this;
    for (const callback of this.listeners.get(event.type) || []) callback.call(this, event);
    return !event.defaultPrevented;
  }
  cloneNode(deep = false) {
    const clone = new Element(this.tagName);
    Object.entries(this.attributes).forEach(([name, value]) => clone.setAttribute(name, value));
    if (deep) this.childNodes.forEach(node => clone.appendChild(node.nodeType === 1 ? node.cloneNode(true) : textNode(node.textContent)));
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

function textNode(value) {
  return {
    nodeType: 3, nodeValue: String(value), parentNode: null,
    get textContent() { return this.nodeValue; },
    set textContent(text) { this.nodeValue = String(text); }
  };
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
  const name = element('div', 'ut2-gl__name');
  const title = element('a', 'product-title', 'Занятие ' + productId);
  title.href = '/dev_copy/products.view?product_id=' + productId;
  name.appendChild(title); info.appendChild(name);
  body.appendChild(image); body.appendChild(info);
  form.appendChild(body); card.appendChild(form); column.appendChild(card);
  return column;
}

function nativeQuickFilters() {
  const filters = element('div', 'cm-lr-homepage-search-filters');
  const strip = element('div', 'ut2-scroll-content');
  filters.appendChild(strip);
  for (const [key, label] of [['age', 'Возраст'], ['category', 'Категория']]) {
    const wrap = element('div', 'ut2__horizontal-product-filters-dropdown');
    const control = element('div', '', label);
    control.id = 'sw_elm_filter_' + key;
    control.setAttribute('data-ca-lr-homepage-search-filters-switch', key);
    wrap.appendChild(control); strip.appendChild(wrap);
    // The native mobile template renders popups after the strip.
    const popup = element('div'); popup.id = 'elm_filter_' + key;
    filters.appendChild(popup);
  }
  const apply = element('button', '', 'Показать');
  apply.setAttribute('data-ca-lr-homepage-search-filters-apply', '');
  filters.appendChild(apply);
  return filters;
}

function nativeHomepage(wrapperClass, serverChrome = false) {
  const html = element('html'), body = element('body'); html.appendChild(body);
  let serverShell, serverHeader;
  if (serverChrome) {
    serverShell = element('section', 'talario-ux01'); serverShell.id = 'talario-ux01';
    serverShell.setAttribute('data-ux01-server-shell', 'pending');
    serverHeader = element('header', 'header', 'Таларио');
    const main = element('main', 'talario-ux01__main');
    main.appendChild(element('h1', '', 'Занятия для детей в Красногорске'));
    main.appendChild(element('p', 'ux01-loading', 'Загружаем занятия…'));
    serverShell.appendChild(serverHeader); serverShell.appendChild(main); body.appendChild(serverShell);
  }
  const tyghContent = element('div', 'tygh-content'), content = element('div', 'content-grid');
  body.appendChild(tyghContent); tyghContent.appendChild(content);
  const search = element('div', 'ty-search-block'), searchForm = element('form');
  searchForm.setAttribute('name', 'search_form');
  const input = element('input'); input.setAttribute('type', 'text'); searchForm.appendChild(input);
  const submit = element('button'); submit.setAttribute('type', 'submit'); searchForm.appendChild(submit);
  search.appendChild(searchForm); content.appendChild(search);
  const filters = nativeQuickFilters(); content.appendChild(filters);
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
    createTextNode: textNode,
    querySelector: selector => html.querySelector(selector),
    getElementById: id => html.querySelector('#' + id),
    addEventListener() {}
  };
  const ready = [], events = new Map();
  const jquery = target => {
    if (typeof target === 'function') ready.push(target);
    else return { data: key => key === 'owlCarousel' ? target.nativeOwlApi : undefined };
  };
  jquery.ceEvent = (action, name, callback) => {
    assert.equal(action, 'on'); events.set(name, callback);
  };
  const location = {origin:'https://talario.ru', pathname:'/dev_copy/', search:''};
  vm.runInNewContext(source, {
    document, window: { location }, Tygh: { $: jquery }, URLSearchParams
  }, { filename: 'ux01_homepage.js' });
  return {
    document, ajaxRoot, catalog, grid, columns, products, searchForm, filters, serverShell, serverHeader, jquery, location,
    startup: () => ready.forEach(callback => callback()),
    commoninit: () => { assert.ok(events.has('ce.commoninit')); events.get('ce.commoninit')(); }
  };
}

test('server first-frame chrome is retained while native search and AJAX catalog are attached once', () => {
  const page = nativeHomepage('ut2-gl__wrap', true);
  page.startup();
  const shell = page.document.getElementById('talario-ux01');
  assert.ok(shell === page.serverShell, 'enhancement retains the actual server-rendered shell');
  assert.ok(shell.querySelector('.header') === page.serverHeader, 'header does not get replaced on enhancement');
  assert.equal(shell.querySelectorAll('h1').length, 1);
  assert.equal(shell.querySelectorAll('.ux01-loading').length, 0);
  assert.equal(shell.getAttribute('data-ux01-server-shell'), 'ready');
  assert.ok(shell.querySelector('form[name="search_form"]') === page.searchForm);
  assert.ok(shell.querySelector('#lr_homepage_catalog_42') === page.ajaxRoot);
  page.commoninit();
  assert.equal(shell.querySelectorAll('.header').length, 1);
  assert.equal(shell.querySelectorAll('.query-inner').length, 1);
});

test('an interrupted enhancement leaves transferred native search attached to a recoverable server shell', () => {
  const page = nativeHomepage('ut2-gl__wrap', true);
  const append = Element.prototype.appendChild;
  Element.prototype.appendChild = function(node) {
    if (node === page.filters) throw new Error('Simulated interruption while attaching native filters');
    return append.call(this, node);
  };
  try {
    assert.throws(() => page.startup(), /Simulated interruption/);
  } finally {
    Element.prototype.appendChild = append;
  }
  assert.equal(page.serverShell.getAttribute('data-ux01-server-shell'), 'ready', 'watchdog release must not hide already-transferred controls');
  assert.ok(page.document.querySelector('form[name="search_form"]') === page.searchForm, 'transferred search remains connected to the document');
  assert.ok(page.document.querySelector('#lr_homepage_catalog_42') === page.ajaxRoot, 'catalog not yet moved remains connected too');
});

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

// Native mobile price shape from a1e525d5:
// addons/sd_design_changes/hooks/products/product_multicolumns_list.override.tpl
// emits price-wrap > mix-price-and-button > price + control, rather than the
// already-flat footer a desktop-only fixture would accidentally assume.
test('native nested mobile pricing gives price and Details independent footer positions while retaining native actions', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  const card = page.products[0].querySelector('.ut2-gl__item');
  const form = card.querySelector('form');
  const info = card.querySelector('.ut2-gl__content');
  const title = info.querySelector('.product-title');
  const priceWrap = element('div', 'ut2-gl__price-wrap');
  const mix = element('div', 'ut2-gl__mix-price-and-button');
  const amount = element('div', 'ut2-gl__price');
  amount.appendChild(element('span', 'ty-price', '1 200 ₽'));
  const control = element('div', 'ut2-gl__control');
  const nativeDetails = element('a', 'ty-btn', 'Подробнее');
  nativeDetails.href = title.href;
  let nativeClicks = 0;
  nativeDetails.addEventListener('click', () => { nativeClicks++; });
  control.appendChild(nativeDetails); mix.appendChild(amount); mix.appendChild(control);
  priceWrap.appendChild(mix); info.appendChild(priceWrap);

  page.startup();

  const foot = card.querySelector('.card-foot');
  const stack = foot && foot.querySelector('.price-stack');
  const details = foot && foot.querySelector('a.more');
  assert.ok(stack, 'native displayed price has a footer position');
  assert.ok(stack.parentElement === foot, 'price stack is a direct footer child, outside the native mix wrapper');
  assert.ok(details, 'Details is presented alongside the price');
  assert.ok(details.parentElement === foot, 'Details is a direct footer child so native price width cannot place it');
  assert.ok(details === nativeDetails, 'native Details node and its event listeners are retained');
  assert.ok(amount.closest('.price-stack') === stack, 'the original amount node remains displayed');
  assert.equal(details.href, title.href);
  assert.ok(card.querySelector('form') === form, 'native product form is retained');
  details.dispatchEvent({ type: 'click' });
  assert.equal(nativeClicks, 1);
  page.commoninit();
  assert.equal(card.querySelectorAll('.card-foot').length, 1);
  assert.ok(card.querySelector('a.more') === nativeDetails);
});

test('native quantity controls are not mistaken for the Details product link', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  const card = page.products[0].querySelector('.ut2-gl__item');
  const info = card.querySelector('.ut2-gl__content');
  const title = info.querySelector('.product-title');
  const price = element('div', 'ut2-gl__price-wrap');
  const originalForm = card.querySelector('form');
  const productId = element('input'); productId.setAttribute('type', 'hidden');
  productId.setAttribute('name', 'product_data[1][product_id]'); productId.setAttribute('value', '1');
  const csrf = element('input'); csrf.setAttribute('type', 'hidden');
  csrf.setAttribute('name', 'security_hash'); csrf.setAttribute('value', 'synthetic-fixture-value');
  originalForm.appendChild(productId); originalForm.appendChild(csrf);
  const amount = element('div', 'ut2-gl__price', '0 ₽');
  const control = element('div', 'ut2-gl__control');
  const cart = element('button', 'ty-btn__add-to-cart', 'В корзину');
  cart.setAttribute('type', 'submit'); cart.setAttribute('name', 'dispatch[checkout.add..1]');
  const qty = element('div', 'ty-value-changer');
  const increase = element('a', 'cm-increase ty-value-changer__increase', '+');
  const decrease = element('a', 'cm-decrease ty-value-changer__decrease', '−');
  const otherAction = element('a', 'native-other-action', 'Другое действие');
  otherAction.href = '/dev_copy/cart/';
  qty.appendChild(increase); qty.appendChild(decrease);
  control.appendChild(cart); control.appendChild(qty); control.appendChild(otherAction);
  price.appendChild(control); price.appendChild(amount); info.appendChild(price);
  let quantityClicks = 0;
  increase.addEventListener('click', () => { quantityClicks++; });

  page.startup();

  const details = card.querySelector('a.more');
  assert.equal(details.textContent, 'Подробнее', 'Details remains a real product navigation action');
  assert.equal(details.href, title.href);
  assert.ok(increase.parentElement === qty, 'native quantity structure stays intact');
  assert.ok(decrease.parentElement === qty);
  assert.ok(otherAction.parentElement === control, 'unrelated native links are not promoted to Details');
  assert.ok(cart.parentElement === control, 'native submit action is retained');
  assert.ok(!control.closest('.card-foot'), 'native cart and quantity cannot become extra flex items in the price/Details row');
  assert.ok(control.parentElement === card.querySelector('.ut2-gl__body'), 'native actions have their own connected card row');
  assert.ok(control.classList.contains('ux01-native-actions'));
  assert.ok(control.closest('form') === originalForm, 'native control keeps its original submission form');
  assert.ok(cart.closest('form') === originalForm, 'submit button keeps product and CSRF fields in its form');
  assert.ok(productId.closest('form') === originalForm && csrf.closest('form') === originalForm);
  assert.equal(originalForm.querySelector('[name="product_data[1][product_id]"]').getAttribute('value'), '1');
  assert.equal(originalForm.querySelector('[name="security_hash"]').getAttribute('value'), 'synthetic-fixture-value');
  increase.dispatchEvent({ type: 'click' });
  assert.equal(quantityClicks, 1, 'native quantity event listeners survive enhancement');
  page.commoninit();
  assert.equal(card.querySelectorAll('a.more').length, 1);
  assert.ok(increase.parentElement === qty);
});

test('an unbookable card with an empty native price wrapper displays availability and Details in its footer', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  const card = page.products[0].querySelector('.ut2-gl__item');
  const info = card.querySelector('.ut2-gl__content');
  const title = info.querySelector('.product-title');
  const availability = element('span', '', 'Скоро можно будет забронировать занятие');
  availability.style.color = 'green';
  info.appendChild(availability);
  // This empty node exists in the native template even when it contains no
  // price/control capture. Its presence must not suppress availability.
  const emptyPrice = element('div', 'ut2-gl__price-wrap');
  info.appendChild(emptyPrice);

  page.startup();

  const foot = card.querySelector('.card-foot');
  const stack = foot && foot.querySelector('.price-stack');
  assert.ok(stack, 'availability occupies the footer when no price exists');
  assert.ok(stack.parentElement === foot);
  assert.match(stack.textContent, /Скоро|забронировать/i, 'truthful unavailable/coming-soon state is visible');
  const details = foot.querySelector('a.more');
  assert.ok(details, 'unbookable cards still allow opening their details');
  assert.ok(details.parentElement === foot);
  assert.equal(details.textContent.trim(), 'Подробнее');
  assert.equal(details.href, title.href, 'Details uses the native product URL');
  page.commoninit();
  assert.equal(card.querySelectorAll('.card-foot').length, 1);
  assert.equal(card.querySelectorAll('a.more').length, 1);
});

test('a native price node containing only reload inputs does not suppress availability in the footer', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  const card = page.products[0].querySelector('.ut2-gl__item');
  const info = card.querySelector('.ut2-gl__content');
  const availability = element('span', '', 'Скоро можно будет забронировать через Таларио');
  const priceWrap = element('div', 'ut2-gl__price-wrap');
  const amount = element('div', 'ut2-gl__price ut2-gl__no-price');
  const reload = element('span', 'cm-reload-668 ty-price-update'); reload.id = 'price_update_668';
  const input = element('input'); input.setAttribute('type', 'hidden');
  reload.appendChild(input); amount.appendChild(reload); priceWrap.appendChild(amount);
  info.appendChild(availability); info.appendChild(priceWrap);
  page.startup();
  assert.ok(availability.closest('.card-foot'), 'real unavailable copy is moved into the footer even though the empty amount node exists');
  assert.match(card.querySelector('.price-stack').textContent, /Скоро/);
  assert.ok(card.querySelector('#price_update_668') === reload, 'native reload target is preserved');
  assert.ok(card.querySelector('a.more'));
  page.commoninit();
  assert.equal(card.querySelectorAll('.coming-soon-copy').length, 1);
});

function nativeCarousel(column) {
  // views/products/components/product_icon.tpl at a1e525d5: owl-carousel >
  // cm-gallery-item > anchor > img. Only the main anchor originally carries
  // product_icon_lnk; additional images still belong to the same product.
  const image = column.querySelector('.ut2-gl__image');
  const mainLink = image.querySelector('a.product_icon_lnk');
  const productHref = column.querySelector('.product-title').href;
  mainLink.href = productHref;
  const carousel = element('div', 'ty-thumbs-wrapper owl-carousel cm-image-gallery ty-scroller');
  const mainItem = element('div', 'cm-gallery-item cm-item-gallery ty-scroller__item');
  mainItem.appendChild(mainLink); carousel.appendChild(mainItem);
  const additionalItem = element('div', 'cm-gallery-item cm-item-gallery ty-scroller__item');
  const additionalLink = element('a'); additionalLink.href = productHref;
  const additionalPhoto = element('img'); additionalPhoto.src = '/native-additional-image.jpg';
  additionalPhoto.complete = true; additionalPhoto.naturalWidth = 200; additionalPhoto.naturalHeight = 200;
  additionalLink.appendChild(additionalPhoto); additionalItem.appendChild(additionalLink); carousel.appendChild(additionalItem);
  image.appendChild(carousel);
  return { carousel, links: [mainLink, additionalLink], photos: [mainLink.querySelector('img'), additionalPhoto] };
}

test('native Owl recalculates only after the server first-paint visibility guard is released', () => {
  const page = nativeHomepage('ut2-gl__wrap', true);
  const gallery = nativeCarousel(page.products[0]);
  page.document.documentElement.classList.add('talario-ux01-boot');
  page.jquery.fn = { owlCarousel() {} };
  let updates = 0;
  gallery.carousel.nativeOwlApi = { updateVars() {
    assert.equal(page.document.documentElement.classList.contains('talario-ux01-boot'), false, 'hidden native ancestors must be revealed before measuring slide widths');
    assert.equal(page.serverShell.getAttribute('data-ux01-server-shell'), 'ready');
    updates++;
  } };
  page.startup();
  assert.equal(updates, 1);
});

test('every native carousel image is normalized while its nested product link is preserved', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  const gallery = nativeCarousel(page.products[0]);

  page.startup();

  for (const [index, photo] of gallery.photos.entries()) {
    assert.ok(photo.classList.contains('photo'), 'carousel image ' + (index + 1) + ' participates in the card image layout');
    assert.ok(photo.closest('a') === gallery.links[index], 'the native image link is retained');
    assert.ok(photo.closest('.owl-carousel') === gallery.carousel, 'native carousel ownership is retained');
  }
  assert.ok(gallery.links[0].classList.contains('product_icon_lnk'));
  page.commoninit();
  assert.equal(page.products[0].querySelectorAll('.owl-carousel').length, 1);
});

test('each failed native carousel image gets a fallback inside its own product link', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  const gallery = nativeCarousel(page.products[0]);
  page.startup();

  for (const [index, photo] of gallery.photos.entries()) {
    photo.naturalWidth = 0; photo.naturalHeight = 0;
    photo.dispatchEvent({ type: 'error' });
    assert.equal(photo.hidden, true, 'failed carousel image ' + (index + 1) + ' is hidden');
    const fallback = gallery.links[index].querySelector('.ux01-no-image');
    assert.ok(fallback, 'failed carousel image ' + (index + 1) + ' has a link-local fallback');
    assert.equal(fallback.textContent, 'Фото отсутствует');
    photo.dispatchEvent({ type: 'error' });
    assert.equal(gallery.links[index].querySelectorAll('.ux01-no-image').length, 1, 'repeated errors do not duplicate fallback');
  }
});

test('AJAX-replaced native quick filters are enhanced once on commoninit', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  page.startup();
  assert.equal(page.filters.querySelectorAll('#talario-ux01-all-filters').length, 1);

  const replacement = nativeQuickFilters();
  const parent = page.filters.parentElement;
  parent.insertBefore(replacement, page.filters);
  parent.removeChild(page.filters);
  page.commoninit();

  assert.ok(replacement.querySelector('.ut2-scroll-content').classList.contains('filter-strip'), 'the new strip is enhanced after native AJAX replacement');
  assert.equal(replacement.querySelectorAll('.quick-button').length, 2, 'both native quick-filter controls remain usable');
  assert.equal(replacement.querySelectorAll('#talario-ux01-all-filters').length, 1);
  const allFilters = replacement.querySelector('#talario-ux01-all-filters');
  assert.equal(allFilters.getAttribute('aria-controls'), 'talario-ux01-filter-panel');
  assert.equal(replacement.querySelectorAll('.ux01-popup-apply').length, 2, 'mobile sibling popups receive their apply controls');

  page.commoninit(); page.commoninit();
  assert.equal(replacement.querySelectorAll('#talario-ux01-all-filters').length, 1);
  assert.ok(replacement.querySelector('#talario-ux01-all-filters') === allFilters);
  assert.equal(replacement.querySelectorAll('.ux01-popup-apply').length, 2);
});

test('native next-page URLs remain usable after an AJAX response loses its pagination base URL', () => {
  const page = nativeHomepage('ut2-gl__wrap');
  const button = element('span', 'ut2-load-more');
  const valid = 'https://talario.ru/dev_copy/index.php?dispatch=index.index&page=2&full_render=Y';
  button.setAttribute('data-ut2-load-more-url', valid); page.grid.appendChild(button);
  page.startup();
  assert.equal(button.getAttribute('data-ut2-load-more-url'), valid, 'a correct native URL is unchanged');
  // Exact malformed URL observed after 20 -> 40: the native pagination
  // template concatenates &page=3 to an empty c_url, which fn_url encodes as a path.
  page.location.search = '?dispatch=index.index&page=2&sort_by=price&sort_order=asc&features_hash=322-1336';
  button.setAttribute('data-ut2-load-more-url', 'https://talario.ru/dev_copy/%26page%3D3%26full_render%3DY');
  page.commoninit();
  const repaired = new URL(button.getAttribute('data-ut2-load-more-url'));
  assert.equal(repaired.origin, 'https://talario.ru');
  assert.equal(repaired.pathname, '/dev_copy/index.php');
  assert.equal(repaired.searchParams.get('dispatch'), 'index.index');
  assert.equal(repaired.searchParams.get('page'), '3');
  assert.equal(repaired.searchParams.get('full_render'), 'Y');
  assert.equal(repaired.searchParams.get('sort_by'), 'price');
  assert.equal(repaired.searchParams.get('features_hash'), '322-1336');
  page.commoninit();
  assert.equal(button.getAttribute('data-ut2-load-more-url'), repaired.href, 'reinit is idempotent');
  const other = 'https://example.invalid/dev_copy/%26page%3D3%26full_render%3DY';
  button.setAttribute('data-ut2-load-more-url', other); page.commoninit();
  assert.equal(button.getAttribute('data-ut2-load-more-url'), other, 'adapter does not rewrite links from another origin');
});

