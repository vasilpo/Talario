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
$.ceEvent('on', 'ce.commoninit', function(context) {
const cache_key = _.ab__stickers.runtime.cache_key,
controller_mode = _.ab__stickers.runtime.controller_mode,
lang_code = _.cart_language ?? 'en',
items = context.find('[data-ab-sticker-id]');
for (let timeout in _.ab__stickers.timeouts) {
clearTimeout(_.ab__stickers.timeouts[timeout]);
}
$.ceEvent('trigger', 'ab__stickers.before_process', [context, items]);
if (items.length) {
const ids_to_remove = [],
stickers_storage = JSON.parse(localStorage.getItem(cache_key));
let sticker_ids = [];
items.each(function () {
const item = $(this),
item_sticker = item.attr('data-ab-sticker-id');
sticker_ids.push(item_sticker);
});
if (sticker_ids.length) {
$.ceAjax('request', fn_url('ab__stickers.get_stickers?sl=' + lang_code), {
method: 'post',
hidden: true,
data: {
sticker_ids: sticker_ids,
controller_mode: controller_mode,
},
callback: function (data, params) {
if (!_.ab__stickers.functions.check_is_object_empty(data.stickers_html)) {
const html = data.stickers_html;
let local_storage_assign = {};
local_storage_assign[lang_code] = {};
items.each(function () {
const item = $(this),
item_sticker = item.attr('data-ab-sticker-id');
local_storage_assign[lang_code][item_sticker] = html[item_sticker];
_.ab__stickers.functions.create_sticker(item, html[item_sticker]);
});
if (_.ab__stickers.runtime.caching === true && false) {
if (stickers_storage !== null) {
local_storage_assign = Object.assign(local_storage_assign, stickers_storage);
}
try {
localStorage.setItem(cache_key, JSON.stringify(local_storage_assign));
} catch (e) {
localStorage.clear();
localStorage.setItem(cache_key, JSON.stringify(local_storage_assign));
}
}
$.ceEvent('trigger', 'ab__stickers.stickers_loaded', [context, data, params, items]);
}
}
});
}
}
const wrappers = context.find('.ab-stickers-wrapper,.ab-s-pictograms-wrapper');
if (wrappers.length) {
let prev_w_size = 0;
const resize = function () {
if (prev_w_size !== window.innerWidth) {
prev_w_size = window.innerWidth;
const calculate_size = function () {
wrappers.each(function(i, wrapper) {
wrapper = $(wrapper);
const owlItem = context.find('.ty-product-img .owl-item:first-child, .ty-product-img .ty-pict').first(),
is_pictograms_wrapper = wrapper.hasClass('ab-s-pictograms-wrapper');
let image = owlItem.hasClass('ty-pict') || owlItem.find('.ty-no-image').length ? owlItem : owlItem.find('.ty-pict').first();
if (owlItem.find('.ab__vg-image_gallery_video') && !image.length) {
image = owlItem.find('.ab__vg-image_gallery_video').first();
}
if (
is_pictograms_wrapper ||
owlItem.find('.ty-no-image').length ||
owlItem.find('.ab__vg-image_gallery_video-wrapper').length ||
(image.length && image[0].complete && image[0].offsetHeight > 150)
) {
if (!is_pictograms_wrapper) {
wrapper.css('max-height', image[0].offsetHeight + 'px');
}
let video_gallery_wrapper = wrapper.parents('.ab_vg-images-wrapper').first();
if (is_pictograms_wrapper || !video_gallery_wrapper.length) {
video_gallery_wrapper = wrapper.parent().find('.ab_vg-images-wrapper');
}
if (video_gallery_wrapper.length) {
let images_wrapper = video_gallery_wrapper.find('.ab-vg-vertical,.ab-vertical').first();
if (!images_wrapper.length) {
images_wrapper = video_gallery_wrapper.find('.ty-product-img.ab-vg-vertical-thumbnails');
}
if (images_wrapper.length) {
let offset_position = 'left',
offset_prefix = '',
stickers_wrapper_offset = images_wrapper.get(0).offsetLeft;
if (is_pictograms_wrapper) {
offset_prefix = 'margin-';
}
if (Tygh.language_direction === 'rtl') {
offset_position = 'right';
stickers_wrapper_offset = images_wrapper.get(0).offsetParent.offsetWidth - images_wrapper.get(0).offsetWidth;
}
wrapper.css(offset_prefix + offset_position, stickers_wrapper_offset);
if (is_pictograms_wrapper) {
wrapper.css('width', 'calc(100% - ' + stickers_wrapper_offset + 'px)');
}
}
}
wrapper.removeClass('ab-hidden');
} else {
setTimeout(function () {
calculate_size();
}, 100);
}
});
};
calculate_size();
}
};
let resize_timeout_handler;
const resize_timeout = ($?.fn?.owlCarousel?.options?.responsiveRefreshRate ?? 0) + 1;
const resize_observer = new ResizeObserver((entries, observer) => {
const resize_timeout = ($?.fn?.owlCarousel?.options?.responsiveRefreshRate ?? 0) + 1;
clearTimeout(resize_timeout_handler);
resize_timeout_handler = setTimeout(function () {
resize();
}, resize_timeout);
})
if ($('.ty-product-img').length) {
const resize_timeout = $.fn.owlCarousel?.options.responsiveRefreshRate ?? 202;
const resize_observer = new ResizeObserver($.debounce(resize, resize_timeout));
resize_observer.observe($('.ty-product-img').get(0));
setTimeout(function () { resize() }, resize_timeout);
}
}
});

$.ceEvent('on', 'ab__vg.on_state_change', (info, player, player_state) => {
if (info.video_id && _.ab__video_gallery.settings.on_thumbnail_click === 'image_replace') {
const video_elem = $('#' + (info.video_id));
const event = info.event ?? 'pause';
let stickers_wrapper = video_elem.parents('.ab_vg-images-wrapper').find('.ab-stickers-wrapper');
if (!stickers_wrapper.length) {
stickers_wrapper = video_elem.parents('.ty-product-bigpicture__img').find('.ab-stickers-wrapper');
}
if (stickers_wrapper.length) {
if (event === 'play' && !video_elem.hasClass('ab__vg-image_gallery_video-autoplay')) {
stickers_wrapper.addClass('hidden');
} else {
stickers_wrapper.removeClass('hidden');
}
}
}
});
$(_.doc).on('touchstart', function (e) {
const selectors = '.ab-sticker, .ab-sticker__tooltip',
jelm = $(e.target);
if (!jelm.is(selectors) && !jelm.parents(selectors).length) {
_.ab__stickers.functions.hide_active_stickers();
}
});

if (typeof _.ab__stickers === 'undefined') {
_.ab__stickers = {};
}
if (typeof _.ab__stickers.functions === 'undefined') {
_.ab__stickers.functions = {};
}

_.ab__stickers.functions.create_sticker = function create_sticker(item, sticker_html) {
item.html(sticker_html);
const sticker = item.find('[data-id]');
if (sticker.length) {
const id = sticker.data('id');
const hide_tooltip = function(event) {
$.ceEvent('trigger', 'ab__stickers.hide_sticker_tooltip_pre', [event, sticker, id]);
sticker.parents('.ut2-gl__item').addClass('ab__stickers-hovered');
if ($(event.relatedTarget).data('sticker-id') !== id && $(event.relatedTarget).data('sticker-p-id') !== id) {
clearTimeout(_.ab__stickers.timeouts[id]);
_.ab__stickers.timeouts[id] = setTimeout(function () {
tooltip.removeClass('hovered mobile');
$.ceEvent('trigger', 'ab__stickers.hide_sticker_tooltip_post', [event, sticker, id]);
}, 50);
}
}
const tooltip = $("[data-sticker-id='" + id + "']").first();
sticker.parent().on('touchstart mouseenter', function () {
if (!tooltip.hasClass('moved')) {
$("[data-sticker-id='" + id + "']:not(:first)").remove();
$("[data-sticker-p-id='" + id + "']:not(:first)").remove();
tooltip.appendTo('BODY').addClass('moved');
}
clearTimeout(_.ab__stickers.timeouts[id]);
const s_pos = sticker.get(0).getBoundingClientRect(),
tooltip_w = tooltip.outerWidth(true),
min_tooltip_horizontal_margin = 25;
let window_width = window.innerWidth,
scrollbar_width = 0;
if (window_width === document.body.clientWidth) {
scrollbar_width = 0;
}
window_width = window_width - scrollbar_width;
let tooltip_pos_y = (window.scrollY + s_pos.top + s_pos.height + 8),
tooltip_pos_x = s_pos.left + (s_pos.width / 2) - (tooltip_w / 2);
let rectangle = {
top: tooltip_pos_y,
left: tooltip_pos_x,
right: window_width - (tooltip_pos_x + tooltip_w)
};
if (rectangle.right < min_tooltip_horizontal_margin + scrollbar_width) {
rectangle.left = window_width - tooltip_w - min_tooltip_horizontal_margin;
} else if (rectangle.left < min_tooltip_horizontal_margin) {
rectangle.left = min_tooltip_horizontal_margin;
}
tooltip.css({
top: rectangle.top + 'px',
left: rectangle.left + 'px',
}).addClass('hovered').on('mouseleave', function(e) {hide_tooltip(e)});
if (_.ab__stickers.functions.check_is_mobile_device()) {
tooltip.addClass('mobile');
}
let tooltip_pointer = tooltip.find('.ab-sticker__tooltip-pointer'),
tooltip_pointer_w = tooltip_pointer.outerWidth(true),
pointer_left = "",
pointer_right = "";
if (rectangle.left <= min_tooltip_horizontal_margin) {
pointer_left = s_pos.left + (s_pos.width / 2) - (tooltip_pointer_w / 2) - rectangle.left;
pointer_right = "unset";
} else if (rectangle.right < min_tooltip_horizontal_margin + scrollbar_width) {
pointer_left = "unset";
pointer_right = window_width - s_pos.left - (s_pos.width / 2) - (tooltip_pointer_w / 2) - min_tooltip_horizontal_margin;
pointer_right = pointer_right > 0 ? pointer_right : 0;
}
tooltip_pointer.css({
left: pointer_left,
right: pointer_right
});
$.ceEvent('trigger', 'ab__stickers.show_sticker_tooltip', [tooltip, sticker, id]);
});
sticker.parent().on('mouseleave', function (e) {hide_tooltip(e)});
}
return sticker;
}

_.ab__stickers.functions.close_tooltip = function close_tooltip(btn){
btn = $(btn);
const tooltip = btn.parent(),
id = tooltip.data('data-sticker-id');
clearTimeout(_.ab__stickers.timeouts[id]);
tooltip.css({
'display': 'none',
'top': '-1000px',
});
setTimeout(function(){
tooltip.css('display', '');
}, 50);
}
_.ab__stickers.functions.hide_active_stickers = function hide_active_stickers() {
$(".ab-sticker__tooltip.hovered").each(function(){
const id = this.getAttribute('data-sticker-id');
$('.ab-sticker[data-id="' + id + '"]').trigger('mouseleave');
});
};

_.ab__stickers.functions.check_is_object_empty = function check_is_object_empty(obj) {
return obj == void(0) || Object.keys(obj).length === 0;
}

_.ab__stickers.functions.check_is_mobile_device = function check_is_mobile_device() {
let check = false;
(function(a){
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {
check = true
}
})(navigator.userAgent||navigator.vendor||window.opera);
return check;
};

_.ab__stickers.functions.get_css_rule_properties = function get_css_rule_properties(rule_pattern) {
const selectors = [];
if (document.styleSheets.length) {
[...document.styleSheets].forEach(function (sheet) {
if (
(
sheet.href && sheet.href.includes(location.hostname)
&& sheet.href.split(/[#?]/)[0].split('.').pop().trim() === 'css'
)
&& sheet.cssRules && sheet.cssRules.length
) {
[...sheet.cssRules].forEach(function (rule) {
if (rule.cssRules && rule.cssRules.length) {
const BreakException = {};
try {
[...rule.cssRules].forEach(function (child_rule) {
if (child_rule.selectorText && child_rule.selectorText.indexOf(rule_pattern) > -1 && child_rule.cssText) {
selectors.push(rule.cssText);
throw BreakException;
}
});
} catch (e) {
if (e !== BreakException) throw e;
}
} else if (rule.selectorText && rule.selectorText.indexOf(rule_pattern) > -1 && rule.cssText) {
selectors.push(rule.cssText);
}
});
}
});
}
return selectors;
}
})(Tygh, Tygh.$);