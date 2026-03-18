/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2025   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
const ITEM_SELECTORS = [
'form[name^="product_form_"]',
'form[name^="vendor_products_form_"]',
'form[name^="variations_list_form"]',
'.ut2-pb__sticky-add-to-cart_block'
];
(function (_, $) {
let isAjax = false;
let isDesktop;
function getProductId(el) {
let id = /^dispatch\[checkout\.add\.\.(\d+)]$/.exec(el.name)?.[1]
if (!id) {
id = $(el).attr('ca-product-id') || $(el).closest('form').find('[name$="[product_id]"]').val() || $(el).attr('data-ca-target-id');
}
return id
}
function setAdded(el, amount, isCounter) {
el.removeClass('ut2-btn__options').addClass('ut2-added-to-cart')
isCounter && el.attr('data-added-amount', amount)
}
function removeAmount(el, isVariation) {
el.removeClass('ut2-added-to-cart').attr('data-added-amount', null)
isVariation && el.addClass('ut2-btn__options')
}
function getIsProductPageBlock(el) {
return !!$(el).closest('.ty-product-block__button').length
}
function processButton(el, data) {
const self = $(el)
const productId = getProductId(el)
const isProductPageButton = getIsProductPageBlock(self)
if (!productId) return false
if (!(data.product_variations[productId] || data.products[productId])) return true
const amount = isProductPageButton ? data.products[productId] : (data.product_variations?.[productId] || data.products?.[productId])
const isVariation = el.tagName === 'A' && ['ut2_select_variantion', 'ut2_select_options'].includes(el.dataset.caDialogPurpose)
if (data.isRemoved) {
removeAmount(self, isVariation)
} else if (amount) {
setAdded(self, amount, data.isCounter)
}
}
function setCartState(cart, items) {
let { added = {}, removed = {}, products, product_variations } = cart
const isCounter = _.abt__ut2.settings.product_list.show_cart_status === 'counter'
const btns = items.find('[name^="dispatch[checkout.add.."],[data-ca-product-id].ty-btn__add-to-cart,[data-ca-target-id].ty-btn__add-to-cart,[data-ca-dispatch^="dispatch[checkout.add.."]')
const processBtns = (isRemoved, data) => {
btns.each((_, el) => processButton(el, {
isRemoved,
isCounter,
...data
}))
}
if (!isAjax) {
processBtns(false, { products, product_variations })
return
}
if (Object.keys(removed.products).length || Object.keys(removed.product_variations).length) {
processBtns(true, removed)
}
if (Object.keys(added.products).length || Object.keys(added.product_variations).length) {
processBtns(false, added)
}
}
function setTooltipTitle(el, title) {
el.data('tooltip')?.getTip()?.get(0)?.remove()
el.attr('title', title).data('tooltip', null).ceTooltip()
}
function setWishlistState(wishlist, items) {
const { added, removed, products } = wishlist
const isRemoved = isAjax && removed.length
const stack = isAjax ? (isRemoved ? removed : added) : products
const title = isRemoved ? _.tr('abt__ut2.add_to_wishlist.tooltip') : _.tr('product_in_wishlist')
const dispatch = isRemoved ? 'add' : 'delete'
const addClass = isRemoved ? '' : 'active'
const removeClass = isRemoved ? 'active' : ''
stack.forEach((id) => {
const btns = items.find(`[data-ca-dispatch*="wishlist"][data-ca-dispatch$=".${id}]"]`)
btns.each(function () {
const btn = $(this)
if ((!isRemoved && btn.hasClass(addClass)) || (isRemoved && !btn.hasClass(removeClass))) return
const isProductPageButton = getIsProductPageBlock(btn)
const dispatchValue = !isRemoved && !isProductPageButton ? `dispatch[wishlist.${dispatch}.force.${id}]` : `dispatch[wishlist.${dispatch}..${id}]`;
btn.removeClass(removeClass)
.addClass(addClass)
.attr('data-ca-dispatch', dispatchValue)
.data('caDispatch', dispatchValue)
if (isDesktop) setTooltipTitle(btn, title)
})
})
}
function setCompareState(compare, items) {
const { added, removed, products } = compare
const isRemoved = isAjax && removed.length
const stack = isAjax ? (isRemoved ? removed : added) : products
const addClass = isRemoved ? '' : 'active'
const removeClass = isRemoved ? 'active' : ''
const hrefNew = isRemoved ? 'product_features.add_product&product_id=' : 'product_features.delete_product&product_id='
const hrefOld = isRemoved ? 'product_features.delete_product&product_id=' : 'product_features.add_product&product_id='
const title = isRemoved ? _.tr('add_to_comparison_list') : _.tr('product_added_to_cl')
stack.forEach((id) => {
const btns = items.find(`a[href*="product_features."][href*="product_id=${id}&"]:not([href$="product_features.compare"])`)
btns.each(function () {
const btn = $(this)
if (btn.hasClass(addClass)) return
btn.removeClass(removeClass)
.addClass(addClass)
.attr('href', btn.attr('href').replace(hrefOld, hrefNew))
if (isDesktop) setTooltipTitle(btn, title)
})
})
}
function processProductsState() {
const entities = {
cart: {
'added': [],
'removed': [],
'products': [],
'product_variations': []
},
compare: {
'added': [],
'removed': [],
'products': []
},
wishlist: {
'added': [],
'removed': [],
'products': []
}
};
for (let entity in entities) {
if (_.abt__ut2[entity]) {
for (let state in _.abt__ut2[entity]) {
entities[entity][state] = _.abt__ut2[entity][state];
}
}
}
const {cart, compare, wishlist} = entities;
const items = ITEM_SELECTORS.reduce((stack, selector) => stack.add(selector), $())
if (_.abt__ut2.settings.product_list.show_cart_status !== 'not-show') {
setCartState(cart, items)
}
if (_.abt__ut2.settings.product_list.show_favorite_compare_status === 'Y') {
setCompareState(compare, items)
setWishlistState(wishlist, items)
}
}
function ajaxDoneHandler(...args) {
const { abt_state } = args[3]
if (abt_state) {
isAjax = true
Object.assign(_.abt__ut2, abt_state)
}
}
$.ceEvent('on', 'ce.commoninit', $.debounce(() => {
isDesktop = _.abt__ut2.device == 'desktop'
processProductsState(isAjax)
isAjax = false
}, 200));
$.ceEvent('on', 'ce.ajaxdone', ajaxDoneHandler)
}(Tygh, Tygh.$));
