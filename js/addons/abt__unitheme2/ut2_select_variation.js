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
(function (_, $) {
const isTouch = document.documentElement.classList.contains('touchevents');
const event = isTouch ? 'click' : 'mouseenter';
let requestObj;
function onVariationEnter(e) {
if (isTouch) {
loadVariation(e.currentTarget);
return;
}
const parent = $(this).parent();
if (!parent.hasClass('lv-hover-items')) return
let timer = parent.data('variationChangeTimer');
timer && clearTimeout(timer);
timer = setTimeout(() => {
clearTimeout(parent.data('variationChangeTimer'));
loadVariation(e.currentTarget);
}, 100);
parent.data('variationChangeTimer', timer);
$(this).one('mouseleave', function () {
const timer = parent.data('variationChangeTimer');
timer && clearTimeout(timer);
})
}
function setInitialVariation($el) {
const variant = $(this).children('.active').data('abt--ut2-variations-variant-id');
$el.data('baseVariant', variant);
}
function getInitialVariationElement($el) {
const variation = $el.data('baseVariant');
if (!variation) return null;
return $el.find(`[data-abt--ut2-variations-variant-id="${variation}"]`);
}
function registerVariationEvents() {
const variationItems = $('.lv-hover-items:not(:empty)');
if (isTouch && variationItems.children('a').length > 0 || variationItems.children().length === 0) return;
variationItems.each(function () {
const featureItem = $(this);
const glItem = featureItem.closest('form').parent();
if (!getInitialVariationElement(glItem)) setInitialVariation.call(this, glItem);
featureItem.off(event)
.on(event, `:not(.active)`, onVariationEnter)
if (isTouch || $._data(glItem.get(0), 'events') !== undefined) return;
glItem.on('mouseleave', function () {
const el = getInitialVariationElement(glItem).get(0);
const active = glItem.find('.lv-hover-items [data-abt--ut2-variations-variant-id].active').get(0);
requestObj?.abort();
if (active !== el) loadVariation(el);
})
})
}
$.ceEvent('on', 'ce.commoninit', function (context) {
context.find('.cm-ab-load-select-variation-content').click(function () {
var btn = this;
var product_id = btn.getAttribute('data-ca-product-id');
const combination = btn.getAttribute('data-ca-combination');
var target_id = 'ut2_select_variation_wrapper_' + product_id;
var existing_target = document.getElementById(target_id);
if (existing_target !== null) {
existing_target.remove();
}
var target = $(`<div id="${target_id}" class="ab-mobile-select-variation-container select-variation-dialog hidden"></div>`);
target.appendTo('#' + _.container);
const data = {
product_id: product_id,
get_view_content: target_id,
}
if (combination) {
data.combination = combination;
}
$.ceAjax('request', fn_url('products.ut2_select_variation'), {
method: 'get',
hidden: false,
data: data,
callback: function (res) {
if (res.html[target_id] !== undefined && res.html[target_id] !== null) {
target.html(res[target_id]);
btn.classList.add('loaded');
window.g_fn__showDialog(target, 'type-_select-variation');
$.ceEvent('trigger', 'ce.commoninit', [target]);

$.ceEvent('trigger', 'ut2_load_select_variation', [target, btn]);
}
}
});
});
registerVariationEvents();
});
function variationRequest(data, form, carouselParams){
return $.ceAjax('request', fn_url('abt__ut2_variations.render_product_card'), {
data,
method: 'get',
hidden: true,
get_promise: true,
caching:true,
callback: function (response) {
if (response.product_card) {
const html = $(response.product_card),
new_form = html.find('form');
if (new_form.length) {
form.html(new_form.html());
form.attr('name', new_form.find('input[name="abt__ut2_variations_form_name"]').val());
carouselParams && form.find('.owl-carousel.cm-image-gallery').owlCarousel(carouselParams);
}
$.ceEvent('trigger', 'ce.commoninit', [form.parent()]);
}
}
});
}
function loadVariation(variationLinkEl) {
requestObj?.abort();
const $el = $(variationLinkEl),
form = $el.parents('form').first(),
block_key = form.find('input[name="abt__ut2_variations_block_key"]').val(),
template_name = form.find('input[name="abt__ut2_variations_template_name"]').val(),
variation_id = +$el.data('abt--ut2-variations-variant-id'),
carouselParams = form.find('.owl-carousel.cm-image-gallery')?.data('owlCarousel')?.userOptions,
base_variant = form.parent().data('baseVariant'),
redirect_url = form.find('input[name="redirect_url"]').val();
requestObj = variationRequest({
base_variant,
variation_id,
template_name,
block_key,
redirect_url
}, form, carouselParams);
};
$(_.doc).on('click', '.lv-hover-items .ut2-lv__more', function () {
$(this).addClass('hidden').closest('.ut2-gl__item').attr('data-lv-more', "");
$(this).addClass('hidden').closest('.ty-product-list').attr('data-lv-more', "");
});
}(Tygh, Tygh.$));
