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
(function(_, $) {
var timer;
function fn_abt__ut2_lazy_load(context) {
var w_h = $(window).height() + 400,
w_t = $(window).scrollTop() - 200,
w_b = w_t+w_h,
img,
block;
if (context) {
img = $('img[data-src]', context)
} else {
img = $('img[data-src]:visible')
}
img.each(function () {
var i = $(this),
i_h = $(this).outerHeight(),
i_t = $(this).offset().top,
i_b = i_h+i_t;
if ((i_t >= w_t && i_t <= w_b) || (i_b >= w_t && i_b <= w_b)) {
var tmp = $(new Image()),
src = i.attr('data-src'),
srcset = i.attr('data-srcset');
if (srcset) {
tmp.attr('srcset', srcset);
}
tmp.attr('src', src);
tmp.on('load', function () {
i.css({
opacity: 0
}).attr('src', src).removeAttr('data-src');
if (srcset) {
i.attr('srcset', srcset).removeAttr('data-srcset');
}
i.removeClass('lazyOwl').animate({
opacity: 1
}, 250)
})
}
});
if (context) {
block = $('[data-background-url]', context)
} else {
block = $('[data-background-url]:visible');
}
block.each(function () {
var b = $(this),
b_h = $(this).height(),
b_t = $(this).offset().top,
b_b = b_t+b_h;
if ((b_t >= w_t && b_t <= w_b) || (b_b >= w_t && b_b <= w_b)) {
var tmp = $(new Image()),
src = b.attr('data-background-url');
tmp.attr('src', src);
tmp.on('load', function () {
b.css({
opacity: 0,
'background-image': "url('" + src + "')"
}).removeAttr('data-background-url').animate({
opacity: 1
}, 250)
})
}
})
}
$(window).on('scroll resize', function () {
clearTimeout(timer);
timer = setTimeout(fn_abt__ut2_lazy_load, 100);
});
$('.cm-menu-item-responsive').hover(function () {
fn_abt__ut2_lazy_load($(this));
});
$('.cm-combination').click(function () {
var context = $(this).next('.cm-popup-box');
if (context.length) {
fn_abt__ut2_lazy_load(context);
}
});
$.ceEvent('on', 'ce.commoninit', function (context) {
if ($.isReady) {
setTimeout(() => {fn_abt__ut2_lazy_load(context)}, 100);
}
});
$.ceEvent('on', 'ce.notificationshow', function (context) {
fn_abt__ut2_lazy_load(context);
});
$.ceEvent('on', 'ce.dialogshow', function (context) {
fn_abt__ut2_lazy_load(context);
});
$.ceEvent('on', 'ce.tab.show', function (tab_id, tabs_elm) {
var context = $('#content_' + tab_id);
if (context.length) {
fn_abt__ut2_lazy_load(context);
}
});
$('.ut2-gl__item').hover(function(){
clearTimeout(timer);
timer = setTimeout(fn_abt__ut2_lazy_load, 100);
});
timer = setTimeout(fn_abt__ut2_lazy_load, 100);
}(Tygh, Tygh.$));
