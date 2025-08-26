/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2024   *
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
(function(_, $) {
const ITEMS_COUNT_DEFAULT = 3;
const theme_name = _.ab__video_gallery?.settings?.theme_name;
var scroller_type;
var methods = {
init: function() {
var container = $(this);
var params = {
items_count: container.data('caItemsCount') ? container.data('caItemsCount') : ITEMS_COUNT_DEFAULT,
items_responsive: !!container.data('caItemsResponsive'),
cycle: container.data('caCycle') === 'Y',
vertical: container.data('caVertical') === 'Y',
total_img: methods.countElms(container)
};
if (params.vertical && $(window).width() > 767) {
scroller_type = 'jcarousel';
} else {
scroller_type = 'owlcarousel';
}
if (params.total_img <= params.items_count && container.parents('.ab_vg-images-wrapper').length) {
container.removeClass('owl-carousel');
}
if (
(
params.total_img > params.items_count
|| (!params.vertical && ['bright_theme', 'responsive'].includes(theme_name))
)
|| scroller_type === 'jcarousel'
|| !container.parents('.ab_vg-images-wrapper').length
) {
if (container.data('owl-carousel') || container.data('jcarousel')) {
return true;
}
methods.check(container, params);
} else {
container.addClass('ab__vg_loaded');
}
methods.bind(container);
return true;
},
load: function(container, params) {
if (scroller_type === 'owlcarousel') {
container.owlCarousel({
direction: _.language_direction,
items: params.items_count,
singleItem: params.items_count === 1,
responsive: params.items_responsive,
pagination: false,
navigation: true,
lazyLoad: true,
navigationText: params.items_count === 1 ? ['<i class="icon-left-circle ty-icon-left-circle"></i>', '<i class="icon-right-circle ty-icon-right-circle"></i>'] : ['<i class="icon-left-open-thin ty-icon-left-open-thin"></i>', '<i class="icon-right-open-thin ty-icon-right-open-thin"></i>'],
theme: params.items_count === 1 ? 'owl-one-theme' : 'owl-more-theme',
beforeInit: function beforeInit() {
$.ceEvent('trigger', 'ce.product_image_gallery.inner.beforeInit', [this]);
},
afterInit: function(item) {

}
});
} else {
container.css({
'height': (params.items_count * params.i_height),
'overflow': 'hidden',
'padding': 0,
'margin': 0,
'position': 'relative',
'z-index': 'auto'
});
var wrapper = container.parent();
var nav_prev = $('<i class="icon-up-open ty-icon-up-open hand ty-hand" />').prependTo(wrapper);
var nav_next = $('<i class="icon-down-open ty-icon-down-open hand ty-hand" />').appendTo(wrapper);
var wrapper_height = container.outerHeight(true)+nav_prev.outerHeight(true)+nav_next.outerHeight(true);
if (params.main_image_height < wrapper_height && params.items_count > ITEMS_COUNT_DEFAULT) {
params.items_count--;
container.height(params.items_count * params.i_height);
}
var scrolled = 0;
if (!params.cycle) {
nav_prev.css('visibility', 'hidden');
}
nav_next.on('click', function(){
if ((scrolled + params.items_count) < params.items_amount) {
scrolled += 1;
if ((scrolled + params.items_count) === params.items_amount && !params.cycle) {
nav_next.css('visibility', 'hidden');
}
} else if ((scrolled + params.items_count) === params.items_amount && params.cycle) {
scrolled = 0;
} else {
return false;
}
nav_prev.css('visibility', 'visible');
container.stop(true, false).animate({
scrollTop: scrolled*params.i_height
});
});
nav_prev.on('click', function(){
if (scrolled > 0) {
scrolled -= 1;
if (scrolled === 0 && !params.cycle) {
nav_prev.css('visibility', 'hidden');
}
} else if (scrolled === 0 && params.cycle) {
scrolled = params.items_amount - params.items_count;
} else {
return false;
}
nav_next.css('visibility', 'visible');
container.stop(true, false).animate({
scrollTop: scrolled*params.i_height
});
});
let elem = $('.cm-thumbnails-mini.active', container);
if (elem.length) {
let pos = elem.data('caImageOrder') || 0;
while (scrolled <= (pos - params.items_count)) {
nav_next.trigger('click');
}
}
}
container.addClass('ab__vg_loaded');
},
check: function(container, params) {
if (container.data('owl-carousel') || container.data('jcarousel')) {
return true;
}
const set_image_gallery_wraper_width = function (container, params) {
if ((!params.vertical || $(window).width() < 767) && (['bright_theme', 'responsive'].includes(theme_name))) {
const reserved_width = 50,
max_container_width = params.main_image_width - reserved_width;
if (params.c_width > max_container_width) {
params.c_width = max_container_width;
container.closest('.cm-image-gallery-wrapper').width(params.c_width);
params.items_count = Math.floor(params.c_width / params.i_width);
}
container.closest('.cm-image-gallery-wrapper').width(params.c_width);
} else if (scroller_type === 'owlcarousel') {
container.closest('.cm-image-gallery-wrapper').width(params.c_width);
}
}
if (!params.i_width || !params.i_height) {
var t_elm = false;
var wrapper = container.closest('.ab_vg-images-wrapper');
if ($('.cm-thumbnails-mini', container).length) {
var load = false;
if (!t_elm) {
t_elm = $('.cm-thumbnails-mini:first', container);
var i_elm = $('img', t_elm);
if (i_elm.height() <= 5 || i_elm.width() <= 5) {
load = true;
}
let t_elm_image = null;
if ((t_elm_image = $('.cm-previewer:first', wrapper).find('img')[0] ?? null)) {
if (!t_elm_image.complete && t_elm_image.naturalWidth === 0) {
load = true;
}
}
if (load) {
var check_load = function() {
methods.check(container, params);
};
setTimeout(check_load, 500);
return false;
}
}
} else {
t_elm = $('img:first', container);
}
params.i_width = t_elm.outerWidth(true);
params.i_height = t_elm.outerHeight(true);
params.c_width = params.i_width * params.items_count;
params.main_image_width = $('.cm-preview-wrapper', wrapper).outerWidth(false);
params.main_image_height = $('.cm-preview-wrapper', wrapper).outerHeight(false);
set_image_gallery_wraper_width(container, params);
if (scroller_type !== 'owlcarousel') {
params.items_count = parseInt(params.main_image_height/params.i_height);
params.items_amount = methods.countElms(container);
if (params.items_count < ITEMS_COUNT_DEFAULT) {
params.items_count = params.items_amount < 3 ? params.items_amount : ITEMS_COUNT_DEFAULT;
}
set_image_gallery_wraper_width(container, params);
if ((params.main_image_height > params.items_amount * params.i_height) || params.items_amount <= params.items_count) {
container.addClass('ab__vg_loaded');
return false;
} else {
container.data('jcarousel', true);
}
}
}
return methods.load(container, params);
},
bind: function(container) {
container.click(function(e) {
var jelm = $(e.target);
var pjelm;
var in_elm;
if (scroller_type === 'owlcarousel') {
in_elm = (jelm.parents('.cm-item-gallery') || jelm.parents('div.cm-thumbnails-mini'));
} else {
in_elm = (jelm.parents('.cm-item-gallery') || jelm.parents('.cm-thumbnails-mini'));
}
if (in_elm && !jelm.is('img') && !jelm.has('iframe') && !jelm.has('video')) {
return false;
}
if (jelm.hasClass('cm-thumbnails-mini') || (pjelm = jelm.parents('a:first.cm-thumbnails-mini'))) {
jelm = (pjelm && pjelm.length) ? pjelm : jelm;
var c_id = jelm.data('caGalleryLargeId');
$('#' + c_id).closest('.cm-preview-wrapper').trigger('owl.goTo', $(jelm).data('caImageOrder') || 0);
}
});
},
countElms: function(container) {
if (scroller_type === 'owlcarousel') {
return $('.cm-thumbnails-mini', container).length;
} else {
return $('.cm-thumbnails-mini', container).length;
}
}
};
$.fn.AB_ceProductImageGallery = function( method ) {
if (!$().owlCarousel) {
var gelms = $(this);
$.getScript('js/lib/owlcarousel/owl.carousel.min.js', function() {
gelms.AB_ceProductImageGallery();
});
return false;
}
return $(this).each(function(i, elm) {
var errors = { };
if (methods[method]) {
return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
} else if ( typeof method === 'object' || !method ) {
return methods.init.apply(this, arguments);
} else {
$.error('ty.productimagegallery: method ' + method + ' does not exist');
}
});
};
$.ceEvent('on', 'ce.commoninit', function(context) {
context.find('.cm-image-gallery:not(.ab__vg_loaded)').each(function () {
let self = $(this);
self.AB_ceProductImageGallery();
});
});
$.ceEvent('on', 'ce.commoninit', function (context) {
var wrapper = $('.cm-preview-wrapper', context);
var gallery_items_view = +(_.abt__ut2?.settings?.products[_.abt__ut2?.details_layout]?.multiple_product_images?.[_.abt__ut2?.device]||1);
if (gallery_items_view == 2) {
var item = gallery_items_view,
itemsDesktop = gallery_items_view,
itemsDesktopSmall = 2,
itemsTablet = 1,
itemsMobile = 1;
}
if (gallery_items_view == 3) {
var item = gallery_items_view,
itemsDesktop = gallery_items_view,
itemsDesktopSmall = 2,
itemsTablet = 2,
itemsMobile = 1;
}
var desktop = [1400, itemsDesktop],
desktopSmall = [1230, itemsDesktopSmall],
tablet = [1040, itemsTablet],
mobile = [900, itemsMobile];
if (wrapper.length) {
let params = {
direction: _.language_direction,
lazyLoad: true,
pagination: false,
center: true,
addClassActive: true,
beforeInit: function beforeInit() {
$.ceEvent('trigger', 'ce.product_image_gallery.beforeInit', [this]);
},
afterInit: function (item) {
var thumbnails = $('.cm-thumbnails-mini', item.parents('[data-ca-previewer]')),
previewers = $('.cm-image-previewer', item.parents('[data-ca-previewer]')),
previousScreenX = 0,
newScreenX = 0,
swipeThreshold = 7;
previewers.each(function (index, elm) {
$(elm).data('caImageOrder', index);
});
thumbnails.on('click', function () {
item.trigger('owl.goTo', $(this).data('caImageOrder') ? $(this).data('caImageOrder') : 0);
});
item.on('touchstart', function (e) {
previousScreenX = e.changedTouches[0].screenX;
});
item.on('touchmove', function (e) {
newScreenX = e.changedTouches[0].screenX;
if (Math.abs(newScreenX - previousScreenX) > swipeThreshold && e.cancelable) {
e.preventDefault();
}
previousScreenX = newScreenX;
});
$('.cm-image-previewer.hidden', item).toggleClass('hidden', false);
$.ceEvent('trigger', 'ce.product_image_gallery.ready');
},
beforeMove: function (item) {
$('.ty-image-zoom__flyout--visible').removeClass('ty-image-zoom__flyout--visible');
},
afterMove: function (item) {
var _parent = item.parent();
$('.cm-thumbnails-mini', _parent)
.toggleClass('active', false);
var elmOrderInGallery = $('.owl-item.active', item).index();
$('[data-ca-image-order=' + elmOrderInGallery + ']', _parent)
.toggleClass('active', true);
$('.owl-carousel.cm-image-gallery', _parent)
.trigger('owl.goTo', elmOrderInGallery);
$.ceEvent('trigger', 'ce.product_image_gallery.image_changed', [wrapper, item]);
}
}
if($('.ty-quick-view__wrapper').length) {
params = {
...params,
singleItem: true,
items: 1,
navigation: false
}
} else if(gallery_items_view > 1 ) {
params = {
...params,
singleItem: false,
items: item,
itemsDesktop: desktop,
itemsDesktopSmall: desktopSmall,
itemsTablet: tablet,
itemsMobile: mobile,
navigation: true,
navigationText: ['<i class="icon-left-open ty-icon-left-open hand ty-hand" />', '<i class="icon-right-open ty-icon-right-open hand ty-hand" />']
}
} else {
params = {
...params,
singleItem: true,
items: 1,
navigation: 'abt__ut2' in _ ,
navigationText: ['<i class="icon-left-open ty-icon-left-open hand ty-hand" />', '<i class="icon-right-open ty-icon-right-open hand ty-hand" />']
}
}
wrapper.owlCarousel(params);
}
});
})(Tygh, Tygh.$);
