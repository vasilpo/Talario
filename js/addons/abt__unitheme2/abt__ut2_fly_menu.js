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
$.ceEvent('on', 'ce.commoninit', function(context) {
let flyMenuExists = context.find('.ut2-fm');
if(flyMenuExists.length){
$('.ut2-mt').on('click', function () {
$(this).toggleClass('active').parent().toggleClass('toggle-it');
});
if (_.abt__ut2.device === 'desktop') {
$('.ut2-lfl').hover(function () {
var parent = $(this);
var child = parent.find('.ut2-slw');
if (child.length) {
var child_pos = child[0].getBoundingClientRect();
if (child_pos.top < 0) {
child.addClass('no-translate');
} else if (child_pos.bottom > $(window).height()) {
child.addClass('no-translate bottom');
}
}
}, function () {
});

} else {
$('.ut2-lfl i').on('click', function () {
var item = $(this);
var parent = item.parent();
var siblings = parent.siblings('.ut2-lfl, .ut2-lsl');
siblings.toggleClass('hidden');
var back_to_main = parent.parents('.ut2-fm').find('.ut2-fm__link-back');
if (back_to_main.hasClass('hidden')) {
back_to_main.removeClass('hidden');
} else if (!parent.hasClass('ut2-lsl')) {
back_to_main.addClass('hidden');
}
parent.toggleClass('active');
});
$('.ut2-lfl > p, .ut2-lsl > p').click(function(){
var p_elem = $(this);
var parent = p_elem.parent();
if (!parent.hasClass('active')) {
p_elem.next().trigger('click');
}
});
$('.ut2-fm__link-back').on('click', function () {
var wrapper = $(this).addClass('hidden').parent();
wrapper.find('.ut2-lfl, .ut2-lsl').removeClass('hidden active');
});
}
}
});
$('.ut2-sw .ut2-menu-opener').on('click', function () {
$('.ut2-sp-n.open').click();
})
}(Tygh, Tygh.$));