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
window.g_mmq__mobile = window.matchMedia('(max-width: 767px), (max-width: 1023px) and (orientation: landscape) and (hover:none)');
window.g_var__opened_dialog_counter = 0;
window.g_fn__lockPage = function () {
if ( window.g_mmq__mobile.matches ) {
if ( !$('body').is('.js--page-state_-locked') ) {
$('body')
.data('page_scroll_top', $(document).scrollTop())
.css({
'inline-size': '100%',
'position': 'fixed',
'inset-block-start': $('body').data('page_scroll_top') * -1 + 'px',
'inset-inline-start': 0
})
.addClass('js--page-state_-locked');
}
}
}
window.g_fn__unLockPage = function () {
if ( window.g_mmq__mobile.matches ) {
if ( $('body').is('.js--page-state_-locked') ) {
setTimeout(function () {
$('body').removeAttr('style');
$(document).scrollTop($('body').data('page_scroll_top'));
$('body').removeData('scroll_top').removeClass('js--page-state_-locked');
}, 0);
}
}
}
window.g_fn__showDialog = function($this_dialog, modal_type) {
let $all_opened_except_this = {};
let $parent_opened = {};
let $new_active = {};
$all_opened_except_this = $('.js--dialog-state-_opened');
window.g_var__opened_dialog_counter = $all_opened_except_this.length;
if (window.g_var__opened_dialog_counter > 0) {
$parent_opened = $all_opened_except_this.filter('.js--dialog-state-_active').addClass('js--dialog-state-_parent');
$all_opened_except_this.not($parent_opened).removeClass('js--dialog-state-_parent');
$all_opened_except_this
.removeClass('js--dialog-state-_active')
.addClass('js--dialog-state-_ancestor');

} else {
if ($('body').is('.sticky-panel')) {
$('body').addClass('js_page-state_-dialog-with-sticky-bp');
}
window.g_fn__lockPage();
$('html').addClass('js_modal_-open');

}
window.g_var__opened_dialog_counter++;
$new_active = $this_dialog.addClass('js--dialog-state-_opened js--dialog-state-_active');
switch (modal_type) {
case 'type-_select-variation':
$('html').addClass('js_select-variation-dialog_-open');
$(document).on('click.add_to_cart_in_select_variation_dialog', '.select-variation-dialog .ty-btn__add-to-cart',
function () {
$.ceEvent('one', 'ce.ajaxdone', function () {
window.g_fn__hideDialog($new_active, 'type-_select-variation');
});
});
$new_active.find('.ut2-btn-close').on('click', function () {
event.stopPropagation();
event.preventDefault();
window.g_fn__hideDialog($new_active, 'type-_select-variation');
});
break;
case 'type-_notif-extended':
if ( !$('html').is('.js_notif-dialog_-open') ) {
$('html').addClass('js_notif-dialog_-open');
}
break;
}
}
window.g_fn__hideDialog = function($this_dialog, modal_type) {
let $all_opened = $('.js--dialog-state-_opened');
let $all_opened_except_this = {};
let $ancestors_opened = {};
let $parent_opened = {};
let $new_active = {};
let $child_active = {};
if (
(modal_type === 'type-_select-variation' && !$('html').is('.js_select-variation-dialog_-open') && !$all_opened.filter('.select-variation-dialog').length) ||
(modal_type === 'type-_notif-extended' && !$('html').is('.js_notif-dialog_-open') && !$all_opened.filter('.notification-content-extended').length) ||
(modal_type === 'type-_ui-dialog' && !$('html').is('.dialog-is-open') && !$all_opened.filter('.ui-dialog.ui-widget').length)
) {
return;
}
if ($this_dialog.length > 0) {
$this_dialog.removeClass('js--dialog-state-_active js--dialog-state-_opened js--dialog-state-_ancestor js--dialog-state-_parent');
if (modal_type === 'type-_select-variation') {
$this_dialog.remove();
}
}
$all_opened_except_this = $('.js--dialog-state-_opened');
window.g_var__opened_dialog_counter = $all_opened_except_this.length;
switch (modal_type) {
case 'type-_select-variation':
$(document).off('click.add_to_cart_in_select_variation_dialog');
$('html').removeClass('js_select-variation-dialog_-open');
break;
case 'type-_notif-extended':


if (!$all_opened_except_this.filter('.notification-content-extended').length) {
$('html').removeClass('js_notif-dialog_-open');
}
break;
}
if (window.g_var__opened_dialog_counter === 0) {
$('html').removeClass('js_modal_-open');
window.g_fn__unLockPage();
if ($('body').is('.js_page-state_-dialog-with-sticky-bp')) {
setTimeout(function () {
$('body').removeClass('js_page-state_-dialog-with-sticky-bp');
}, 0);
}

} else {
$ancestors_opened = $all_opened_except_this.not('.js--dialog-state-_active');
$child_active = $all_opened_except_this.filter('.js--dialog-state-_active');
if ($child_active.length > 0) {
if ($ancestors_opened.length > 0) {
$parent_opened = $ancestors_opened.last().addClass('js--dialog-state-_parent');

}
} else {
if ($ancestors_opened.length > 0) {
$new_active = $ancestors_opened
.last()
.removeClass('js--dialog-state-_ancestor js--dialog-state-_parent')
.addClass('js--dialog-state-_active');
$parent_opened = $ancestors_opened.not($new_active).last();
if ($parent_opened.length) {
$parent_opened.addClass('js--dialog-state-_parent');
}

}
}
}
}
$.ceEvent('on', 'ce.dialogshow', function ($d, e, u) {
let popup_id = $d.attr('id').replace('content_', '');
let $opener = $('#opener_' + popup_id);
if ($opener.length) {
let $qty = $opener.closest('form').find('input[name$="[amount]"]');
if ($qty.length) {
$d.find('input[name$="[amount]"]').val($qty.val());
}
}
window.g_fn__showDialog($d.parent('.ui-dialog'), 'type-_ui-dialog');
});

$.ceEvent('on', 'ce.dialogclose', function ($d) {
window.g_fn__hideDialog($d.parent('.ui-dialog'), 'type-_ui-dialog');
});
$(document).on('click.fix_change_options_dialog_close', '.ty-product-bundles-get-option-variants .ty-btn[name*="change_options"]',
function () {
let $this_dialog = $(this).closest('.ui-dialog.ui-widget');
window.g_fn__hideDialog($this_dialog, 'type-_ui-dialog');
});

if ( window.g_mmq__mobile.matches ) {
$(document).on('click.before_open_notif_extended_dialog',
'body:not(.js--page-state_-locked) .ty-btn__add-to-cart[name*="checkout.add"]' + ',' +
'body:not(.js--page-state_-locked) .ty-btn__add-to-cart[data-ca-dispatch*="checkout.add"]' + ',' +
'body:not(.js--page-state_-locked) .ut2-add-to-wish[data-ca-dispatch*="wishlist.add"]:not(.active)' + ',' +
'body:not(.js--page-state_-locked) .ut2-add-to-compare[data-ca-target-id*="compar"]:not(.active)'
,
function () {
window.g_fn__lockPage();
$.ceEvent('one', 'ce.ajaxdone', function () {
setTimeout(function () {
if (!$('body').children('.notification-content-extended').length) {
window.g_fn__unLockPage();
}
}, 400);
});
});
$(document).on('click.fix_page_jump_bug', 'body:not(.js--page-state_-locked) .ut2-pb__button.ty-product-block__button .ut2-add-to-wish.active[data-ca-dispatch*="wishlist.delete"]',
function () {
window.g_fn__lockPage();
$.ceEvent('one', 'ce.ajaxdone', function () {
window.g_fn__unLockPage();
});
});
}
$.ceEvent('on', 'ce.notificationshow', function (t, e, u) {
const notificationExtended = Array.from(t).find((n)=>n.className.includes('cm-notification-content-extended'));
if (!notificationExtended) return;
window.g_fn__showDialog($(notificationExtended), 'type-_notif-extended');
const targetNode = notificationExtended.parentNode;
const config = { attributes: false, childList: true, subtree: false};
const callback = function(mutationList, observer) {
for (const mutation of mutationList) {
if (mutation.type !== 'childList') continue;
for (node of mutation.removedNodes) {
if (node === notificationExtended) {
window.g_fn__hideDialog($(notificationExtended), 'type-_notif-extended');
observer.disconnect();
return;
}
}
}
};
const observer = new MutationObserver(callback);
observer.observe(targetNode, config);
});

$(window).on('load.process_immediate_dialog', function () {
setTimeout(function () {
let $immediate_notif_dialog = $('body').children('.notification-content-extended').not('.js--dialog-state-_opened');
let $immediate_notif_dialog__close_btns;
if ($immediate_notif_dialog.length) {
$immediate_notif_dialog__close_btns = $immediate_notif_dialog.find('.cm-notification-close');
$immediate_notif_dialog__close_btns.one('click.close_immediate_notif_dialog', function() {
window.g_fn__hideDialog($immediate_notif_dialog, 'type-_notif-extended');
});
window.g_fn__showDialog($immediate_notif_dialog, 'type-_notif-extended');
}
}, 1000);
});


window.fn_alert = function (msg, not_strip) {
msg = not_strip ? msg : fn_strip_tags(msg);
$.ceNotification('show', {
type: 'I',
title: '',
message: '<div class="notification-extended-stuffing">' + msg + '</div>',
message_state: 'I'
});
}

}(Tygh, Tygh.$));