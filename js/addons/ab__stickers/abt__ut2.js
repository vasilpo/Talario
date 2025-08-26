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
const intervals = {};
let grid_inited = false;
$.ceEvent('on', 'ce.commoninit', function (context) {
if (!grid_inited) {
const ut2_gl_item = context.find('.ut2-gl__item').first().get(0);
if (ut2_gl_item) {
let selectors = _.ab__stickers.functions.get_css_rule_properties('.ut2-gl__item:hover').join('').replaceAll(':hover', '.ab__stickers-hovered');
let aspect_ratio = ut2_gl_item.style?.aspectRatio;
if (aspect_ratio) {
selectors += '.grid-list .ut2-gl__item.ab__stickers-hovered{aspect-ratio:' + aspect_ratio + ' !important;}';
}
if ($(ut2_gl_item).find('.ut2-gl__body.content-on-hover').length) {
selectors += _.ab__stickers.functions.get_css_rule_properties('.ut2-gl__body:hover').join('').replaceAll(':hover', '.ab__stickers-hovered')
}
const style_element = _.doc.createElement('style');
style_element.innerText = selectors;
_.body.append(style_element);
grid_inited = true;
}
}
});
$.ceEvent('on', 'ab__stickers.before_process', function (context, items) {
context.find('.ab-stickers-container__price_before').each(function (i, container) {
container = $(container);
if (container.parents('.ut2-simple-list__price').length && !_.ab__stickers.functions.check_is_mobile_device()) {
container.parents('.ut2-simple-list__price').first().before(container);
}
});
context.find('.ab-stickers-container__price_after').each(function (i, container) {
container = $(container);
if (container.parents('.ut2-template-small__item-price').length) {
container.parent().append(container);
}
if (container.parents('.ut2-simple-list__price').length && !_.ab__stickers.functions.check_is_mobile_device()) {
container.parents('.ut2-simple-list__price').first().after(container);
}
if (container.parents('.ty-compact-list__price').length) {
container.parent().append(container);
}
if (container.parents('.ut2-gl__price').length) {
container.parent().append(container);
}
if (container.parents('.ut2-pl__price').length) {
container.parent().append(container);
}
});
});
$.ceEvent('on', 'ab__stickers.stickers_loaded', function (context, data, params, items) {
if (_?.ab__stickers?.settings?.abt__ut2?.less?.product_list?.use_elements_alignment === 'use') {
const grid_lists = items.parents('.grid-list');
const grid_list_name_lines = Number.parseInt(_?.abt__ut2?.settings?.product_list?.products_multicolumns?.lines_number_in_name_product[_?.abt__ut2?.device] ?? "0")
let name_lines_rule = false;
if (Number.isInteger(grid_list_name_lines) && grid_list_name_lines > 1) {
name_lines_rule = true;
}
grid_lists.each(function (i, grid_list) {
grid_list = $(grid_list);
let price_block_height = 0;
let name_block_height = 0;
if (name_lines_rule) {
grid_list.find('.ut2-gl__item').each(function (i, item) {
item = $(item);
const name_block = item.find('.ut2-gl__name');
name_block_height = Math.max(name_block_height, name_block.height());
});
}
grid_list.find('.ut2-gl__item').each(function (i, item) {
item = $(item);
const price_wrap = item.find('.ut2-gl__price-wrap');
if (price_wrap.find('.ab-stickers-container').length) {
const height = price_wrap.height();
price_block_height = Math.max(price_block_height, height);
}
});
if (price_block_height) {
grid_list.find('.ut2-gl__item').each(function (i, item) {
item = $(item);
const price_wrap = item.find('.ut2-gl__price-wrap');
const name_block = item.find('.ut2-gl__name');
let height = price_block_height;
if (name_block.height() < name_block_height) {
height += name_block_height - name_block.height();
}
price_wrap.css('min-height', height + 'px');
});
}
});
}
if (typeof window.fn_abt__ut2_calc_cell === 'function') {
fn_abt__ut2_calc_cell(context);
}
});
$.ceEvent('on', 'ab__stickers.hide_sticker_tooltip_pre', function (event, sticker) {
sticker.parents('.ut2-gl__item').addClass('ab__stickers-hovered').find('.ut2-gl__body').addClass('ab__stickers-hovered');
});
$.ceEvent('on', 'ab__stickers.hide_sticker_tooltip_post', function (event, sticker) {
sticker.removeClass('ab__sticker__tooltip-active').parents('.ut2-gl__item').removeClass('ab__stickers-hovered').find('.ut2-gl__body').removeClass('ab__stickers-hovered');
});
$.ceEvent('on', 'ab__stickers.show_sticker_tooltip', function (tooltip, sticker) {
sticker.addClass('ab__sticker__tooltip-active');
});

const get_elem_unique_id = function (elm) {
if (!elm.data('ab--stickers-unique-id')) {
elm.data('ab--stickers-unique-id', Date.now().toString(36) + Math.random().toString(36).slice(2, 9));
}
return elm.data('ab--stickers-unique-id');
};

const clear_elem_interval = function (id) {
if (typeof intervals[id] === 'number') {
clearInterval(intervals[id]);
}
intervals[id] = false;
};

const reset_elem_interval = function (elm, callback, interval = 600) {
const elm_unique_id = get_elem_unique_id(elm);
clear_elem_interval(elm_unique_id);
intervals[elm_unique_id] = setInterval(callback, interval);
};

const process_img_wrap_hover = function (img_wrap) {
const img_wrap_unique_id = get_elem_unique_id(img_wrap);
if (img_wrap.hasClass('ab__stickers__img-hover_start')) {
if (!img_wrap.find('.ab__sticker__tooltip-active').length) {
img_wrap.addClass('ab__stickers__img-hover');
}
} else {
clear_elem_interval(img_wrap_unique_id);
}
};
$.ceEvent('one', 'ce.commoninit', function (context) {
let img_wrap_selectors = '';
if (_?.abt__ut2?.settings?.product_list?.ab__stickers?.hide_stickers_on_hover[_?.abt__ut2?.device] === 'Y') {
let selectors = '.ut2-gl__image, .ut2-pl__image, .ty-scroller-list__img-block, .ty-compact-list__image';
img_wrap_selectors += img_wrap_selectors.trim() ? img_wrap_selectors + ', ' + selectors : selectors;
}
if (_?.abt__ut2?.settings?.products?.ab__stickers?.hide_stickers_on_hover[_?.abt__ut2?.device] === 'Y') {
img_wrap_selectors += img_wrap_selectors.trim() ? img_wrap_selectors + ', ' + '.ty-product-img' : '.ty-product-img';
}
if (img_wrap_selectors.trim()) {
$(_.doc).on('touchstart mouseenter', img_wrap_selectors, function (e) {
const img_wrap = $(this);
let real_img_wrap = img_wrap;
if (img_wrap.hasClass('ty-scroller-list__img-block')) {
real_img_wrap = img_wrap.parents('.ty-scroller-list__item').first();
} else if (img_wrap.hasClass('ty-product-img')) {
real_img_wrap = img_wrap.parents('.ut2-pb__img').first();
}
real_img_wrap.addClass('ab__stickers__img-hover_start');
reset_elem_interval(real_img_wrap, () => {process_img_wrap_hover(real_img_wrap)});
});
$(_.doc).on('mouseleave', img_wrap_selectors, function (e) {
const img_wrap = $(this);
let real_img_wrap = img_wrap;
const related_target = $(e.relatedTarget);
const related_target_img_wrap = related_target.parents('.ut2-gl__image').first();
if (img_wrap.hasClass('ty-scroller-list__img-block')) {
real_img_wrap = img_wrap.parents('.ty-scroller-list__item').first();
} else if (img_wrap.hasClass('ty-product-img')) {
real_img_wrap = img_wrap.parents('.ut2-pb__img').first();
}
if (!related_target_img_wrap.length || !related_target_img_wrap.is(real_img_wrap)) {
real_img_wrap.removeClass('ab__stickers__img-hover_start');
reset_elem_interval(real_img_wrap, () => {process_img_wrap_hover(real_img_wrap)});
}
real_img_wrap.removeClass('ab__stickers__img-hover');
});
}
});
})(Tygh, Tygh.$);