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
function fn_abt__ut2_calc_cell(context){
if (fn_abt__ut2_calc_cell.timers === undefined) {
fn_abt__ut2_calc_cell.timers = [];
}
context.each((k,elem) => {
if (fn_abt__ut2_calc_cell.timers[elem]) {
clearTimeout(fn_abt__ut2_calc_cell.timers[elem]);
}
fn_abt__ut2_calc_cell.timers[elem] = setTimeout(fn_abt__ut2_calc_cell_execute, 100, $(elem));
})
}
function fn_abt__ut2_calc_cell_execute(context){
const [ctx, iteration = 0] = arguments;
$('div.grid-list:visible', context).each(function(){
var cell = $(this).find('[class*="ty-column"][data-ut2-grid="first-item"] > [class*="ut2-gl__item"]');
const height = cell.outerHeight(), width = cell.outerWidth();
if (height === 0 && iteration < 3) {
setTimeout(() => fn_abt__ut2_calc_cell(ctx, iteration + 1), 300)
return
}
$(this).css('--gl-item-height', height).css('--gl-item-width', width);
});
}

window.g_fn__getBrowserName = function (user_agent) {
if (user_agent.includes("Firefox") || user_agent.includes("FxiOS") || user_agent.includes("Focus")) {
return "Mozilla Firefox";
} else if (user_agent.includes("SamsungBrowser")) {
return "Samsung Internet";
} else if (user_agent.includes("Opera") || user_agent.includes("OPR")) {
return "Opera";
} else if (user_agent.includes("Edge")) {
return "Microsoft Edge (Legacy)";
} else if (user_agent.includes("Edg")) {
return "Microsoft Edge (Chromium)";
} else if (user_agent.includes("Chrome") || user_agent.includes("CriOS")) {
return "Google Chrome or Chromium";
} else if (user_agent.includes("Safari") && /Apple/.test(user_agent) && (!!window.ApplePaySetupFeature || !!window.safari)) {
return "Apple Safari";
} else {
return "unknown";
}
}
const g__browser_name = window.g_fn__getBrowserName(navigator.userAgent);
const g_flag__is_firefox_android_browser = /^Linux a/.test(navigator.oscpu);
const g_flag__is_chromium = !!window.chrome;
const g_flag__is_full_screen = !!(document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
window.g_fn__checkTouchScreenDevice = function() {
let touch_points_enabled = false;
if ('maxTouchPoints' in navigator && Number.isFinite(navigator.maxTouchPoints)) {
touch_points_enabled = navigator.maxTouchPoints > 0 && navigator.maxTouchPoints !== 256;
} else if ('msMaxTouchPoints' in navigator && Number.isFinite(navigator.msMaxTouchPoints)) {
touch_points_enabled = navigator.msMaxTouchPoints > 0 && navigator.msMaxTouchPoints !== 256;
} else if ('ontouchend' in document || 'touchend' in document || (window.DocumentTouch && document instanceof DocumentTouch)) {
touch_points_enabled = true;
} if (fn__checkTouchEvent()) {
touch_points_enabled = true;
} else {
const mQ = matchMedia?.('(pointer:coarse)');
if (mQ?.media === '(pointer:coarse)') {
touch_points_enabled = !!mQ.matches;
} else if ('orientation' in window || typeof screen.orientation !== 'undefined') {
touch_points_enabled = true;
} else {
const user_agent = navigator.userAgent;
touch_points_enabled =
/\b(BlackBerry|webOS|iPhone|IEMobile)\b/i.test(user_agent) ||
/\b(Android|Windows Phone|iPad|iPod)\b/i.test(user_agent) ||
/\b(Opera Mini|fennec|nokia|windows mobile)\b/i.test(user_agent) ||
/Mobi/i.test(user_agent);
}
}
return touch_points_enabled;
function fn__checkTouchEvent() {
try {
document.createEvent('TouchEvent');
return true;
} catch (e) {
return false;
}
}
};
const g_flag__is_touch_screen = window.g_fn__checkTouchScreenDevice();
const g_flag__is_mobile_device = /Mobi/i.test(navigator.userAgent);
const g_flag__is_mobile_device__1_alt = window.matchMedia('(pointer:coarse)').matches && !window.matchMedia('(pointer:fine)').matches && window.matchMedia('(hover:none)').matches;
const g_flag__is_mobile_device__2_alt = typeof navigator.userAgentData !== 'undefined' && navigator.userAgentData != null && navigator.userAgentData.mobile;
const g_flag__is_iPad_desktop_device = navigator.userAgent.includes('Mac') && g_flag__is_touch_screen && navigator.vendor != null && navigator.vendor.match(/Apple Computer, Inc./) && /Apple/.test(navigator.userAgent) && !/Edge/i.test(navigator.userAgent) && !window.MSStream;
const g_flag__is_iPad_mobile_device = /iPad/i.test(navigator.userAgent) && navigator.vendor != null && navigator.vendor.match(/Apple Computer, Inc./) && /Apple/.test(navigator.userAgent) && !/Edge/i.test(navigator.userAgent) && !window.MSStream;
const g_flag__is_iPad_device = g_flag__is_iPad_desktop_device || g_flag__is_iPad_mobile_device;
const g_flag__is_iPhone_device = /iPhone|iPod/i.test(navigator.userAgent) && navigator.vendor != null && navigator.vendor.match(/Apple Computer, Inc./) && /Apple/.test(navigator.userAgent) && /Mobi/i.test(navigator.userAgent) && !/Edge/i.test(navigator.userAgent) && !window.MSStream;
const g_flag__is_iOS_iPadOS_device = g_flag__is_iPad_device || g_flag__is_iPhone_device;
const g_flag__is_iOS_device__alt = typeof navigator.standalone === 'boolean';
const g_flag__is_android_device = navigator.userAgent.toLowerCase().includes('android');
const g_flag__is_android_tablet_device = g_flag__is_android_device && !/Mobi/i.test(navigator.userAgent);
const g_flag__is_android_phone_device = g_flag__is_android_device && /Mobi/i.test(navigator.userAgent);
const g_flag__is_rtl = document.documentElement.getAttribute('dir') === 'rtl';
const g_flag__is_devel_domain =
(window.location.href.indexOf('.test.abt.team') > -1) ||
(window.location.href.indexOf('192.168') > -1) ||
(window.location.href.indexOf('localhost') > -1);
window.rt__overall_top_stuck_height = 0;
const g__ut2_top_panel_height = parseInt($(':root').css('--ut2-top-panel-height'));
const g__ut2_header_1st_row_height = parseInt($(':root').css('--ut2-header-height'));
const g__ut2_header_2nd_row_height_1 = parseInt($(':root').css('--ut2-header__2nd-row-_height-_1'));
const g__ut2_header_2nd_row_height_2 = parseInt($(':root').css('--ut2-header__2nd-row-_height-_2'));
let g__page_scroll_distance_top_prior = 0;
let g__page_scroll_distance_top_current = 0;
(function(_, $) {

window.fn_utility__debounceUnderscore_fs = function (func, wait, immediate) {
let timeout;
return function executedFunction() {
const context = this;
const args = arguments;
const later = function () {
timeout = null;
if (!immediate) func.apply(context, args);
};
const callNow = immediate && !timeout;
clearTimeout(timeout);
timeout = setTimeout(later, wait);
if (callNow) func.apply(context, args);
};
}
window.fn_utility__throttleWithLeadingAndTrailing = function (func, wait = 50, { leading = true, trailing = true } = {}) {
let last = 0, timeout = null, lastArgs, lastContext;
const invoke = (time) => {
last = time;
func.apply(lastContext, lastArgs);
lastArgs = lastContext = null;
};
const later = () => {
const now = Date.now(), rem = wait - (now - last);
if (rem <= 0 || rem > wait) {
if (trailing || !leading) invoke(now);
timeout = null;
} else timeout = setTimeout(later, rem);
};
return function (...args) {
const now = Date.now();
if (!last && !leading) last = now;
lastArgs = args;
lastContext = this;
const rem = wait - (now - last);
if (rem <= 0 || rem > wait) {
if (timeout) clearTimeout(timeout), timeout = null;
invoke(now);
} else if (!timeout && trailing) timeout = setTimeout(later, rem);
};
}


$(window).on('load.global', function (event) {
setTimeout(function () {
$('body').addClass('js_window_-loaded');
}, 0);
});


$(document).ready(function () {
if (g_flag__is_iOS_device__alt) {
$('body').addClass('js_ios');
}
fn__toggleCheckoutCvv2NoteTtip();
fn__preventPageZoomingOnPinchAndDoubleTap();
fn__keepTrackAjaxProcessThroughPreloaderDisplayProperty();
fn__catalogMenu();
fn__headerAndPanels();
fn__horizontalProductFilter();
fn__optimizeSubmitOrderButtonDisplay();
fn__stickyBottomPanel();
fn__unfoldEntireHeading();
$(window).on('scroll.global', function (event) {
fn_debounced__getDropdownPopupTopClearanceToWindowEdge($('.top-menu-grid-vertical.ty-dropdown-box .ty-dropdown-box__title[id]'));
fn_debounced__getDropdownPopupTopClearanceToWindowEdge($('.ty-horizontal-product-filters-dropdown__wrapper'));
fn_debounced__getHorizontalMenuPopupTopClearanceToWindowEdge();
fn__tweakProductStickyColumn();
});
$(window).on('resize.global', function (event) {
fn_abt__ut2_calc_cell($(document));
fn_debounced__getDropdownPopupTopClearanceToWindowEdge($('.top-menu-grid-vertical.ty-dropdown-box .ty-dropdown-box__title[id]'));
fn_debounced__getDropdownPopupTopClearanceToWindowEdge($('.ty-horizontal-product-filters-dropdown__wrapper'));
fn_debounced__getHorizontalMenuPopupTopClearanceToWindowEdge();
fn_debounced__tweakProductStickyColumn();
});
$(document).on('click.global', function(event) {
let $click_target = $(event.target);
if (_.abt__ut2.device === 'desktop' && window.matchMedia('(max-width: 775px)').matches) {
if (! $click_target.closest('.ut2-horizontal-product-filters-dropdown').length ) {
$('.ty-horizontal-product-filters-dropdown__wrapper.open').trigger('click');
}
}
});
$(document).on('click.copy_sku', '.ty-sku-item .ut2_copy', function () {
window.g_fn__copySkuTextToClipboard($(this).find('.ut2--sku-text'));
});
fn_abt__ut2_calc_cell($(document));
$.ceEvent('on', 'ce.commoninit', function(context) {
fn_abt__ut2_calc_cell(context);
});
$.ceEvent('on', 'ce.tab.show', function(tab_id, tabs_elm) {
fn_abt__ut2_calc_cell(tabs_elm.parent());
});
});


function fn__optimizeSubmitOrderButtonDisplay() {
let submit_order_button_inteval_id;
let $submit_order_item;
setTimeout(function () {
submit_order_button_inteval_id = setInterval(function () {
$submit_order_item = $('.litecheckout__submit-order.litecheckout__item');
if ($submit_order_item.length) {
clearInterval(submit_order_button_inteval_id);
if ($submit_order_item.is(':hidden')) {
$submit_order_item.addClass('js_display-submit-order-item');
}
}
}, 200);
setTimeout(function () {
clearInterval(submit_order_button_inteval_id);
}, 2000);
}, 2000);
}


const fn_helper_debounced__keepTrackPageScrollMetrics = window.fn_utility__debounceUnderscore_fs(fn_helper__keepTrackPageScrollMetrics, 100);
function fn_helper__keepTrackPageScrollMetrics() {
g__page_scroll_distance_top_current = Math.round($(window).scrollTop());
$(':root').css('--js_page-_scroll-distance-_top_-current', `${g__page_scroll_distance_top_current}px`);
if (g__page_scroll_distance_top_current > g__page_scroll_distance_top_prior) {
$('body').removeClass('js_page_-scrolled-_up js_page_-scrolled-_none').addClass('js_page_-scrolled-_down');
}
if (g__page_scroll_distance_top_current < g__page_scroll_distance_top_prior) {
$('body').removeClass('js_page_-scrolled-_down js_page_-scrolled-_none').addClass('js_page_-scrolled-_up');
}
if (g__page_scroll_distance_top_current == g__page_scroll_distance_top_prior) {
$('body').removeClass('js_page_-scrolled-_down js_page_-scrolled-_up').addClass('js_page_-scrolled-_none');
}
g__page_scroll_distance_top_prior = g__page_scroll_distance_top_current;
}


function fn__headerAndPanels() {
const setting__enable_sticky_header = _.abt__ut2.settings.general.top_sticky_panel.enable[_.abt__ut2.device] === 'Y';
const setting__enable_sticky_fc_panel = _.abt__ut2.settings.category.show_sticky_panel_filters_and_categories[_.abt__ut2.device] === 'Y';
const setting__add_sticky_bottom_panel = _.abt__ut2.settings.general.sticky_panel.enable_sticky_panel[_.abt__ut2.device] === 'Y';
const body_class_header_stuck = 'fixed-header';
const body_class_whole_header_out_of_viewport = 'js_whole-header-out-of-viewport';
const body_class_fc_panel_stuck = 'sticky-fc-panel';
const body_class_bottom_panel_stuck = 'sticky-panel';
const mmq__header_search_2nd_row_mode = window.matchMedia('(max-width: 1140px)');
const $top_panel = $('.tygh-top-panel');
const top_panel_height = ($top_panel.length > 0 && $top_panel.find('.top-grid').length > 0 && $top_panel.children('[class*=container]').children().length > 0) ? Math.round($top_panel.outerHeight()) : 0;
const $tygh_header = $('.tygh-header');
const $header_rows_parent = $tygh_header.children('[class*=container]');
const $header_allows_h_menu_stuck_top_sentinel = $('.ut2-header-allows-h-menu-stuck-top-sentinel');
const flag__is_header_advanced = $tygh_header.is('.advanced-header');
const flag__is_header_default = $tygh_header.is('.default-header');
const flag__is_header_light = $tygh_header.is('.light-header');
const flag__is_header_light_v1 = $tygh_header.is('.light-header:not(.v2):not(.v3)');
const flag__is_header_light_v2 = $tygh_header.is('.v2');
const flag__is_header_light_v3 = $tygh_header.is('.v3');
const flag__is_header_allows_h_menu = flag__is_header_default || flag__is_header_light_v2;
const flag__is_header_checkout = $tygh_header.is('.litecheckout__header');
const flag__is_header_allows_only_1st_row = flag__is_header_light_v1 || flag__is_header_light_v3 || flag__is_header_checkout;
const $header_1st_row = $header_rows_parent.children('div:nth-child(1)');
let rt__header_1st_row_height;
const $header_2nd_row = $header_rows_parent.children('div:nth-child(2)');
let header_2nd_row_height;
const $header_stuck_row = flag__is_header_advanced ? $header_2nd_row : $header_1st_row;
let header_stuck_height;
let rt__header_stuck_height = 0;
const $fc_panel = $('.top-sticky-panel__filters');
const fc_panel_height = $fc_panel.length > 0 ? parseInt($(':root').css('--sticky-top-panel-height')) : 0;
let rt__fc_panel_stuck_height = 0;
const $product_card = $('.ut2-pb__wrapper');
const $product_mainbar = $('.ut2-pb__right-wrapper');
$(':root').css('--js_top-panel-_height', `${top_panel_height}px`);
$(':root').css('--header-height', `${g__ut2_header_1st_row_height}px`);
function getRealtimeHeader1stRowHeight() {
return ($header_1st_row.length > 0) ? Math.round($header_1st_row.outerHeight()) : 0;
}
rt__header_1st_row_height = getRealtimeHeader1stRowHeight();
$(':root').css('--js_rt_header__1st-row-_height', `${rt__header_1st_row_height}px`);
if ($header_2nd_row.length > 0) {
if (flag__is_header_advanced) {
header_2nd_row_height = g__ut2_header_2nd_row_height_1;
} else if (flag__is_header_allows_h_menu) {
header_2nd_row_height = g__ut2_header_2nd_row_height_2;
} else {
header_2nd_row_height = 0;
}
} else {
header_2nd_row_height = 0;
}
$(':root').css('--menu-height', `${header_2nd_row_height}px`);
if (setting__enable_sticky_header && $header_stuck_row.length > 0) {
if (flag__is_header_advanced) {
header_stuck_height = header_2nd_row_height;
} else if (flag__is_header_default) {
if (mmq__header_search_2nd_row_mode.matches) {
header_stuck_height = rt__header_1st_row_height + g__ut2_header_2nd_row_height_1;
} else {
header_stuck_height = rt__header_1st_row_height;
}
} else {
header_stuck_height = rt__header_1st_row_height;
}
} else {
header_stuck_height = 0;
}
$(':root').css('--js_header_-stuck-_height', `${header_stuck_height}px`);
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
$(':root').css('--js_fc-panel-_height', `${fc_panel_height}px`);
$(':root').css('--js_rt_fc-panel_-stuck-_height', `${rt__fc_panel_stuck_height}px`);
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
if (flag__is_header_light_v1) $tygh_header.addClass('v1');
if ('IntersectionObserver' in window && 'IntersectionObserverEntry' in window && 'intersectionRatio' in window.IntersectionObserverEntry.prototype) {
let fn__fcPanelIntsecObsCB;
let fn__fcPanelIntsecObsOpt;
let fcPanelIntsecObserver;
let fn__headerAdvancedIntsecObsCB;
let headerAdvancedIntsecObsOpt;
let headerAdvancedIntsecObserver;
let fn__headerAllowsHorizontalMenuIntsecObsCB;
let headerAllowsHorizontalMenuIntsecObsOpt;
let headerAllowsHorizontalMenuIntsecObserver;
let fn__headerAllowsOnly1stRowIntsecObsCB;
let headerAllowsOnly1stRowIntsecObsOpt;
let headerAllowsOnly1stRowIntsecObserver;
let fn__wholeHeaderIntsecObsCB;
let wholeHeaderIntsecObsOpt;
let wholeHeaderIntsecObserver;
if (setting__enable_sticky_fc_panel && $fc_panel.length > 0) {
fn__fcPanelIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
const $observed_target = $(entry.target);
$('body').toggleClass(body_class_fc_panel_stuck, !entry.isIntersecting);
if (!entry.isIntersecting) {
if (rt__fc_panel_stuck_height == 0) {
rt__fc_panel_stuck_height = fc_panel_height;
$(':root').css('--js_rt_fc-panel_-stuck-_height', `${rt__fc_panel_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__fc_panel_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
} else {
if (rt__fc_panel_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__fc_panel_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
rt__fc_panel_stuck_height = 0;
$(':root').css('--js_rt_fc-panel_-stuck-_height', `${rt__fc_panel_stuck_height}px`);
}
}
});
}
fn__fcPanelIntsecObsOpt = {
root: null,
rootMargin: `${(header_stuck_height + 1) * -1}px 0px 0px`,
threshold: [1],
}
fcPanelIntsecObserver = new IntersectionObserver(fn__fcPanelIntsecObsCB, fn__fcPanelIntsecObsOpt);
if (!flag__is_header_default) {
fcPanelIntsecObserver.observe($fc_panel.get(0));
}
}
mmq__header_search_2nd_row_mode.addListener(fn__headerSearchModeChanges);
function fn__headerSearchModeChanges(mmq) {
if (setting__enable_sticky_header && $header_stuck_row.length > 0 && flag__is_header_default) {
if (mmq.matches) {
header_stuck_height = getRealtimeHeader1stRowHeight() + g__ut2_header_2nd_row_height_1;
} else {
header_stuck_height = getRealtimeHeader1stRowHeight();
}
$(':root').css('--js_header_-stuck-_height', `${header_stuck_height}px`);
if (rt__header_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__header_stuck_height;
rt__header_stuck_height = header_stuck_height;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
if (setting__enable_sticky_fc_panel && $fc_panel.length > 0) {
fcPanelIntsecObserver.disconnect();
fn__fcPanelIntsecObsOpt.rootMargin = `${(header_stuck_height + 1) * -1}px 0px 0px`;
fcPanelIntsecObserver = new IntersectionObserver(fn__fcPanelIntsecObsCB, fn__fcPanelIntsecObsOpt);
fcPanelIntsecObserver.observe($fc_panel.get(0));
}
}
}
fn__headerSearchModeChanges(mmq__header_search_2nd_row_mode);
if (setting__enable_sticky_header && $header_stuck_row.length > 0) {
if (flag__is_header_advanced) {
fn__headerAdvancedIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
$('body').toggleClass(body_class_header_stuck, !entry.isIntersecting);
if (!entry.isIntersecting) {
if (rt__header_stuck_height == 0) {
rt__header_stuck_height = header_2nd_row_height;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
} else {
if (rt__header_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
rt__header_stuck_height = 0;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
}
}
});
}
if ($header_1st_row.length > 0) {
headerAdvancedIntsecObsOpt = {
root: null,
rootMargin: '-1px 0px 0px',
threshold: [0],
}
} else {
headerAdvancedIntsecObsOpt = {
root: null,
rootMargin: '-1px 0px 0px',
threshold: [1],
}
}
headerAdvancedIntsecObserver = new IntersectionObserver(fn__headerAdvancedIntsecObsCB, headerAdvancedIntsecObsOpt);
if ($header_1st_row.length > 0) {
headerAdvancedIntsecObserver.observe($header_1st_row.get(0));
} else {
headerAdvancedIntsecObserver.observe($header_stuck_row.get(0));
}
}
if (flag__is_header_allows_h_menu && $header_allows_h_menu_stuck_top_sentinel.length > 0) {
fn__headerAllowsHorizontalMenuIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
$('body').toggleClass(body_class_header_stuck, !entry.isIntersecting);
if (!entry.isIntersecting) {
if (rt__header_stuck_height == 0) {
if (flag__is_header_default) {
if (mmq__header_search_2nd_row_mode.matches) {
rt__header_stuck_height = rt__header_1st_row_height + g__ut2_header_2nd_row_height_1;
} else {
rt__header_stuck_height = rt__header_1st_row_height;
}
} else {
rt__header_stuck_height = rt__header_1st_row_height;
}
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
} else {
if (rt__header_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
rt__header_stuck_height = 0;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
}
}
});
}
headerAllowsHorizontalMenuIntsecObsOpt = {
root: null,
rootMargin: '-1px 0px 0px',
threshold: [0],
}
headerAllowsHorizontalMenuIntsecObserver = new IntersectionObserver(fn__headerAllowsHorizontalMenuIntsecObsCB, headerAllowsHorizontalMenuIntsecObsOpt);
headerAllowsHorizontalMenuIntsecObserver.observe($header_allows_h_menu_stuck_top_sentinel.get(0));
}
if (flag__is_header_allows_only_1st_row && !flag__is_header_checkout) {
if (top_panel_height > 0) {
fn__headerAllowsOnly1stRowIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
$('body').toggleClass(body_class_header_stuck, !entry.isIntersecting);
if (!entry.isIntersecting) {
if (rt__header_stuck_height == 0) {
rt__header_stuck_height = rt__header_1st_row_height;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
} else {
if (rt__header_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
rt__header_stuck_height = 0;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
}
}
});
}
headerAllowsOnly1stRowIntsecObsOpt = {
root: null,
rootMargin: '-1px 0px 0px',
threshold: [1],
}
headerAllowsOnly1stRowIntsecObserver = new IntersectionObserver(fn__headerAllowsOnly1stRowIntsecObsCB, headerAllowsOnly1stRowIntsecObsOpt);
headerAllowsOnly1stRowIntsecObserver.observe($header_stuck_row.get(0));
} else {
$(window).off('.header_allows_only_1st_row').on('scroll.header_allows_only_1st_row resize.header_allows_only_1st_row', function() {
fn_enclosed__keepTrackStickyHeaderAllowsOnly1stRow();
});
fn_enclosed__keepTrackStickyHeaderAllowsOnly1stRow();
function fn_enclosed__keepTrackStickyHeaderAllowsOnly1stRow() {
let flag__is_page_scrolled = $(document).scrollTop() > 0;
if ($header_stuck_row.data('is_page_scrolled') !== flag__is_page_scrolled) {
$header_stuck_row.data('is_page_scrolled', flag__is_page_scrolled);
$('body').toggleClass(body_class_header_stuck, flag__is_page_scrolled);
if (flag__is_page_scrolled) {
if (rt__header_stuck_height == 0) {
rt__header_stuck_height = rt__header_1st_row_height;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
} else {
if (rt__header_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
rt__header_stuck_height = 0;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
}
}
}
}
}
}
}
if ($header_1st_row.length > 0) {
if ('ResizeObserver' in window) {
let header_1st_row_rsz_obs_timeout_id;
const fn__header1stRowRszObsCB = (entryList, obsInstance) => {
clearTimeout(header_1st_row_rsz_obs_timeout_id);
header_1st_row_rsz_obs_timeout_id = setTimeout(() => {
for (let entry of entryList) {
const $observed_target = $(entry.target);
let rt__target_width = Math.round('borderBoxSize' in entry ? entry.borderBoxSize[0].inlineSize : entry.contentRect.width);
let rt__target_height = Math.round('borderBoxSize' in entry ? entry.borderBoxSize[0].blockSize : entry.contentRect.height);
if (rt__header_1st_row_height != rt__target_height) {
rt__header_1st_row_height = rt__target_height;
$(':root').css('--js_rt_header__1st-row-_height', `${rt__header_1st_row_height}px`);
if (setting__enable_sticky_header && $header_stuck_row.length > 0) {
if (flag__is_header_default) {
if (mmq__header_search_2nd_row_mode.matches) {
header_stuck_height = rt__header_1st_row_height + g__ut2_header_2nd_row_height_1;
} else {
header_stuck_height = rt__header_1st_row_height;
}
} else if (!flag__is_header_advanced) {
header_stuck_height = rt__header_1st_row_height;
}
}
$(':root').css('--js_header_-stuck-_height', `${header_stuck_height}px`);
if (rt__header_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__header_stuck_height;
rt__header_stuck_height = header_stuck_height;
$(':root').css('--js_rt_header_-stuck-_height', `${rt__header_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__header_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
if (setting__enable_sticky_fc_panel && $fc_panel.length > 0) {
fcPanelIntsecObserver.disconnect();
fn__fcPanelIntsecObsOpt.rootMargin = `${(header_stuck_height + 1) * -1}px 0px 0px`;
fcPanelIntsecObserver = new IntersectionObserver(fn__fcPanelIntsecObsCB, fn__fcPanelIntsecObsOpt);
fcPanelIntsecObserver.observe($fc_panel.get(0));
}
}
}
}, 400);
}
const header1stRowRszObserver = new ResizeObserver(fn__header1stRowRszObsCB);
header1stRowRszObserver.observe($header_1st_row.get(0));
}
}
if ($tygh_header.length > 0) {
fn__wholeHeaderIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
$('body').toggleClass(body_class_whole_header_out_of_viewport, !entry.isIntersecting);
});
}
wholeHeaderIntsecObsOpt = {
root: null,
rootMargin: '-1px 0px 0px',
threshold: [0],
}
wholeHeaderIntsecObserver = new IntersectionObserver(fn__wholeHeaderIntsecObsCB, wholeHeaderIntsecObsOpt);
wholeHeaderIntsecObserver.observe($tygh_header.get(0));
}
if ($product_card.length > 0 && $product_mainbar.length > 0) {
if ('ResizeObserver' in window) {
$product_mainbar.each(function () {
const $this_product_mainbar = $(this);
const fn__productMainbarRszObsCB = (entryList, obsInstance) => {
for (let entry of entryList) {
let rt__target_height = Math.round('borderBoxSize' in entry ? entry.borderBoxSize[0].blockSize : entry.contentRect.height);
$this_product_mainbar.css('--js_rt_product-card__mainbar-_height', `${rt__target_height}px`);
fn_debounced__tweakProductStickyColumn();
}
}
const header1stRowRszObserver = new ResizeObserver(fn__productMainbarRszObsCB);
header1stRowRszObserver.observe($this_product_mainbar.get(0));
});
}
}
}
fn_sub__setUpStickyBuyPanel();
fn_sub__keepTrackSEOpbTabsPanel();
}


function fn_sub__keepTrackSEOpbTabsPanel() {
const setting__add_seo_pb_tabs_panel = $('html').hasClass('seo-pb-tabs-panel');
const $seo_pb_tabs_panel = $('.ab-spt-floating-panel');
const seo_pb_tabs_panel_height = $seo_pb_tabs_panel.length > 0 ? $seo_pb_tabs_panel.outerHeight() : 0;
let rt__seo_pb_tabs_panel_stuck_height = 0;
let fn__bodyClassObsCB;
let bodyClassObsOpt;
let bodyClassObserver;
let flag__seo_pb_tabs_panel_was_stuck = $('body').hasClass('ab-spt-fixed');
$(':root').css('--js_seo-pb-tabs-panel-_height', `${seo_pb_tabs_panel_height}px`);
$(':root').css('--js_rt_seo-pb-tabs-panel_-stuck-_height', `${rt__seo_pb_tabs_panel_stuck_height}px`);
if (setting__add_seo_pb_tabs_panel && $seo_pb_tabs_panel.length > 0) {
if ('MutationObserver' in window) {
fn__bodyClassObsCB = function (mutationList, observer) {
mutationList.forEach(function (mutationRecord) {
if (mutationRecord.type === 'attributes' && mutationRecord.attributeName === 'class') {
if ($(mutationRecord.target).hasClass('ab-spt-fixed') && !flag__seo_pb_tabs_panel_was_stuck) {
if (rt__seo_pb_tabs_panel_stuck_height == 0) {
rt__seo_pb_tabs_panel_stuck_height = seo_pb_tabs_panel_height;
$(':root').css('--js_rt_seo-pb-tabs-panel_-stuck-_height', `${rt__seo_pb_tabs_panel_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__seo_pb_tabs_panel_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
} else if (!$(mutationRecord.target).hasClass('ab-spt-fixed') && flag__seo_pb_tabs_panel_was_stuck) {
if (rt__seo_pb_tabs_panel_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__seo_pb_tabs_panel_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
rt__seo_pb_tabs_panel_stuck_height = 0;
$(':root').css('--js_rt_seo-pb-tabs-panel_-stuck-_height', `${rt__seo_pb_tabs_panel_stuck_height}px`);
}
}
flag__seo_pb_tabs_panel_was_stuck = $(mutationRecord.target).hasClass('ab-spt-fixed');
}
});
}
bodyClassObsOpt = {
attributes: true,
attributeFilter: ['class'],
}
bodyClassObserver = new MutationObserver(fn__bodyClassObsCB);
bodyClassObserver.observe($('body').get(0), bodyClassObsOpt);
}
}
}


function fn_sub__setUpStickyBuyPanel() {
const setting__show_sticky_buy_panel = _.abt__ut2.settings.products.view.show_sticky_panel_add_to_cart[_.abt__ut2.device];
const html_class_sticky_buy_panel_exists = 'js_sticky-buy-panel_-exists';
const $buy_panel_stuck_sentinel = $('.ut2-pb__button.ty-product-block__button .ty-btn__add-to-cart');
const product_id = $buy_panel_stuck_sentinel.length > 0 ? window.fn_utility__getDigitsAtStringEnd($buy_panel_stuck_sentinel.attr('id')) : null;
const $buy_panel = $('.ut2-pb #ut2_pb__sticky_add_to_cart.cm-reload-' + product_id);
let buy_panel_height = 0;
let rt__buy_panel_top_stuck_height = 0;
let rt__buy_panel_bottom_stuck_height = 0;
let $buy_panel_top;
let $buy_panel_bottom;
const body_class_buy_panel_stuck = 'sticky-add-to-cart';
const $bottom_panels_wrapper = $('.ut2-sticky-panel__wrap');
let $copied_buy_panel;
$('html').removeClass(html_class_sticky_buy_panel_exists);
if (setting__show_sticky_buy_panel !== 'none' && $buy_panel_stuck_sentinel.length > 0 && product_id !== null && $buy_panel.length > 0) {
buy_panel_height = parseInt($('body').css('--sticky-add-to-cart-height'));
$buy_panel_top = $buy_panel.filter('.position-top');
$buy_panel_bottom = $buy_panel.filter('.position-bottom');
$('html').addClass(html_class_sticky_buy_panel_exists);
if (setting__show_sticky_buy_panel === 'top') {
$(':root').css('--js_buy-panel-top-_height', `${buy_panel_height}px`);
$(':root').css('--js_buy-panel-bottom-_height', '0px');
} else {
$(':root').css('--js_buy-panel-top-_height', '0px');
$(':root').css('--js_buy-panel-bottom-_height', `${buy_panel_height}px`);
}
}
$(':root').css('--js_rt_buy-panel-top_-stuck-_height', `${rt__buy_panel_top_stuck_height}px`);
$(':root').css('--js_rt_buy-panel-bottom_-stuck-_height', `${rt__buy_panel_bottom_stuck_height}px`);
if (setting__show_sticky_buy_panel !== 'none' && $buy_panel_stuck_sentinel.length > 0 && product_id !== null && $buy_panel.length > 0) {
$copied_buy_panel = $bottom_panels_wrapper.find('#ut2_pb__sticky_add_to_cart');
if ($copied_buy_panel.length) {
$copied_buy_panel.remove();
}
$buy_panel.prependTo($bottom_panels_wrapper).show();
$(window).off('.buy_panel').on('scroll.buy_panel resize.buy_panel', function() {
fn_enclosed__keepTrackStickyBuyPanelSentinel();
});
fn_enclosed__keepTrackStickyBuyPanelSentinel();
function fn_enclosed__keepTrackStickyBuyPanelSentinel() {
const $sentinel = $('.ut2-pb__button.ty-product-block__button .ty-btn__add-to-cart');
if ($sentinel.length > 0) {
let sentinel_rect = $sentinel.get(0).getBoundingClientRect();
let flag__is_sentinel_out_of_bounds = sentinel_rect.top < (parseInt($(':root').css('--js_rt_header_-stuck-_height')) - sentinel_rect.height) ||
sentinel_rect.top > ($(window).height() - parseInt($(':root').css('--sticky-bottom-panel-height')));
if ($sentinel.data('is_out_of_bounds') !== flag__is_sentinel_out_of_bounds) {
$sentinel.data('is_out_of_bounds', flag__is_sentinel_out_of_bounds);
$('body').toggleClass(body_class_buy_panel_stuck, flag__is_sentinel_out_of_bounds);
if (flag__is_sentinel_out_of_bounds) {
if (setting__show_sticky_buy_panel === 'top') {
if (rt__buy_panel_top_stuck_height == 0) {
rt__buy_panel_top_stuck_height = buy_panel_height;
$(':root').css('--js_rt_buy-panel-top_-stuck-_height', `${rt__buy_panel_top_stuck_height}px`);
window.rt__overall_top_stuck_height += rt__buy_panel_top_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
}
} else {
if (rt__buy_panel_bottom_stuck_height == 0) {
rt__buy_panel_bottom_stuck_height = buy_panel_height;
$(':root').css('--js_rt_buy-panel-bottom_-stuck-_height', `${rt__buy_panel_bottom_stuck_height}px`);
}
}
} else {
if (setting__show_sticky_buy_panel === 'top') {
if (rt__buy_panel_top_stuck_height > 0) {
window.rt__overall_top_stuck_height -= rt__buy_panel_top_stuck_height;
$(':root').css('--js_rt_overall-top_-stuck-_height', `${window.rt__overall_top_stuck_height}px`);
rt__buy_panel_top_stuck_height = 0;
$(':root').css('--js_rt_buy-panel-top_-stuck-_height', `${rt__buy_panel_top_stuck_height}px`);
}
} else {
if (rt__buy_panel_bottom_stuck_height > 0) {
rt__buy_panel_bottom_stuck_height = 0;
$(':root').css('--js_rt_buy-panel-bottom_-stuck-_height', `${rt__buy_panel_bottom_stuck_height}px`);
}
}
}
}
}
}
}
}


$.ceEvent('on', 'ce.ajaxdone', function (elms, inline_scripts, params, response_data, response_text) {
if (elms.length && typeof params === 'object' && params.hasOwnProperty('result_ids') && typeof params.result_ids !== 'undefined') {
if (params.result_ids.includes('product_detail_page')) {
fn_sub__setUpStickyBuyPanel();
}
}
});


const fn_debounced__tweakProductStickyColumn = window.fn_utility__debounceUnderscore_fs(fn__tweakProductStickyColumn, 400);
function fn__tweakProductStickyColumn() {
const $product_card = $('.ut2-pb__wrapper');
const $product_mainbar = $('.ut2-pb__right-wrapper');
if ($product_card.length > 0 && $product_mainbar.length > 0 && _.abt__ut2.device !== 'mobile' && window.matchMedia('(min-width: 768px)').matches) {
$product_mainbar.each(function () {
const $this_product_mainbar = $(this);
let this_product_mainbar_height = parseInt($this_product_mainbar.css('--js_rt_product-card__mainbar-_height'));
let this_product_mainbar_top = parseInt($(':root').css('--js_header_-stuck-_height')) + parseInt($(':root').css('--js_buy-panel-top-_height')) + parseInt($(':root').css('--js_seo-pb-tabs-panel-_height')) + parseInt($(':root').css('--gap-s'));
if (this_product_mainbar_top + this_product_mainbar_height >= document.documentElement.clientHeight) {
$product_card.addClass('js_product-col_-sticky-off');
} else {
$product_card.removeClass('js_product-col_-sticky-off');
}
});
}
}


function fn__catalogMenu() {
const $v_menu_onsight = $('.ut2-v__menu').not('.ty-dropdown-box__content .ut2-v__menu');
$v_menu_onsight.each(function () {
const $this_menu = $(this);
const this_menu_Min_width = parseInt($this_menu.css('min-inline-size'));
const this_menu_Max_width = parseInt($this_menu.css('max-inline-size'));
let timeoutId;
if ($this_menu.find('.ut2-menu__item').not('.item-1st-no-drop').length && $this_menu.find('.ut2-menu__submenu').length) {
$this_menu.addClass('js_multi-level');
if ('ResizeObserver' in window) {
let resizeObserver = new ResizeObserver(entries => {
clearTimeout(timeoutId);
timeoutId = setTimeout(() => {
for (let entry of entries) {
let rt__width = Math.floor('borderBoxSize' in entry ? entry.borderBoxSize[0].inlineSize : entry.contentRect.width);
let rt__height = Math.floor('borderBoxSize' in entry ? entry.borderBoxSize[0].blockSize : entry.contentRect.height);
const $observed_this_menu = $(entry.target);
$this_menu.css("--js_rt__menu-v-top-level-_width", rt__width + "px");
}
}, 400);
});
resizeObserver.observe($this_menu.get(0));
}
}
});
$(document).on('click.toggle_v_menu_top_item_submenu', '.ut2-v__menu .ut2-menu__item:not(.item-1st-no-drop) a.ut2-menu__link', function () {
if (window.matchMedia('(min-width: 768px) and (hover: none)').matches) {
event.preventDefault();
let $touched_link = $(this);
let $parent_item = $touched_link.closest('.ut2-menu__item');
let $parent_item__siblings = $parent_item.siblings('.ut2-menu__item');
$parent_item.addClass('js_touched');
$parent_item__siblings.removeClass('js_touched');
}
if (window.matchMedia('(max-width: 767px) and (hover: hover)').matches) {
event.preventDefault();
$(this).siblings('.ty-menu__item-toggle').removeClass('ty-menu__item-toggle-active');
}
});
$(document).on(
'click.toggle_v_menu_top_item_submenu',
'.ut2-menu__submenu__carrier.cascading .ut2-menu__2nd-item__header:not(.no-items),' +
'.ut2-menu__submenu__carrier.cascading .ut2-menu__2nd-item__header:not(.no-items) a.ut2-menu__2nd-link',
function () {
if (window.matchMedia('(min-width: 768px) and (hover: none)').matches) {
event.preventDefault();
let $touched_el = $(this);
let $parent_item = $touched_el.closest('.ut2-menu__2nd-item');
let $parent_item__siblings = $parent_item.siblings('.ut2-menu__2nd-item');
$parent_item.addClass('js_touched');
$parent_item__siblings.removeClass('js_touched');
}
});
fn__delayingHorizontalCatalogMenuDropdownOpening();
}


function fn__delayingHorizontalCatalogMenuDropdownOpening() {
if (window.matchMedia('(min-width: 768px) and (hover: hover)').matches) {
const open_delay = 300;
$(document).on('mouseenter.open_h_menu_top_item_submenu', '.ut2-h__menu .ut2-menu__item:not(.item-1st-no-drop)', function () {
const $this_1st_item = $(this);
let open_timer_id = $this_1st_item.data('open-timer-id');
let close_timer_id = $this_1st_item.data('close-timer-id');
let flag__open_state = $this_1st_item.data('flag-open-state') ? $this_1st_item.data('flag-open-state') : false;
clearTimeout(close_timer_id);
if (!flag__open_state) {
open_timer_id = setTimeout(function () {
$this_1st_item.addClass('js_open');
flag__open_state = true;
$this_1st_item.data('flag-open-state', flag__open_state);
}, open_delay - 1);
$this_1st_item.data('open-timer-id', open_timer_id);
}
});
$(document).on('mouseleave.close_h_menu_top_item_submenu', '.ut2-h__menu .ut2-menu__item:not(.item-1st-no-drop)', function () {
const $this_1st_item = $(this);
let open_timer_id = $this_1st_item.data('open-timer-id');
let close_timer_id = $this_1st_item.data('close-timer-id');
let flag__open_state = $this_1st_item.data('flag-open-state') ? $this_1st_item.data('flag-open-state') : true;
clearTimeout(open_timer_id);
if (flag__open_state) {
close_timer_id = setTimeout(function () {
$this_1st_item.removeClass('js_open');
flag__open_state = false;
$this_1st_item.data('flag-open-state', flag__open_state);
}, open_delay);
$this_1st_item.data('close-timer-id', close_timer_id);
}
});
}
}


function fn__horizontalProductFilter() {
const $h_filter_root = $('.ut2-hz-filters');
$h_filter_root.each(function () {
const $this_h_filter_root = $(this);
const $this_h_filter_scrollable = $this_h_filter_root.find('.ut2-scroll-content');
$this_h_filter_scrollable.on('scroll', function () {
$this_h_filter_scrollable.find('.cm-combination.open').trigger('click');
});
});
}


function fn_sub__computeHfilterPopupSubtractedSpace($h_filter_popup) {
if (_.abt__ut2.device !== 'mobile') {
if ($h_filter_popup.is(':visible')) {
const $h_filter_popup__title = $h_filter_popup.find('.ty-horizontal-product-filters-dropdown__title');
const $h_filter_popup__search = $h_filter_popup.find('.ty-product-filters__search');
const $h_filter_popup__more_less_btn = $h_filter_popup.find('.ut2-more-btn');
const $h_filter_popup__no_items_found = $h_filter_popup.find('.ty-product-filters__no-items-found');
const $h_filter_popup__tools = $h_filter_popup.find('.ty-product-filters__tools');
let h_filter__popup__subtracted_space;
h_filter__popup__subtracted_space = parseInt($h_filter_popup.css('padding-block-start')) + parseInt($h_filter_popup.css('padding-block-end'));
if ($h_filter_popup__title.is(':visible')) h_filter__popup__subtracted_space += Math.ceil($h_filter_popup__title.outerHeight(true));
if ($h_filter_popup__search.is(':visible')) h_filter__popup__subtracted_space += Math.ceil($h_filter_popup__search.outerHeight(true));
if ($h_filter_popup__more_less_btn.is(':visible')) h_filter__popup__subtracted_space += Math.ceil($h_filter_popup__more_less_btn.outerHeight(true));
if ($h_filter_popup__no_items_found.is(':visible')) h_filter__popup__subtracted_space += Math.ceil($h_filter_popup__no_items_found.outerHeight(true));
if ($h_filter_popup__tools.is(':visible')) h_filter__popup__subtracted_space += Math.ceil($h_filter_popup__tools.outerHeight(true));
$h_filter_popup.css('--js_rt__dropdown-popup-_subtracted-space', `${h_filter__popup__subtracted_space}px`);
return h_filter__popup__subtracted_space;
}
}
}


function fn__stickyBottomPanel() {
let $sticky_bottom_panel;
let $sticky_bottom_panel_overlay;
if (_.abt__ut2.device === 'mobile') {
$sticky_bottom_panel = $('.ut2-sticky-panel');
$sticky_bottom_panel_overlay = $('.ut2-sticky-panel__item__overlay');
if ($sticky_bottom_panel.length > 0) {
$('html').addClass('js_sticky-bottom-panel_-exists');
}
if ($sticky_bottom_panel_overlay.length > 0) {
$(document).on('click.sticky_bottom_panel_overlay', '.ut2-sticky-panel__item__overlay', function () {
$(this).siblings('.cm-combination').trigger('click');
});
}
}
}


$(document).on('ce:combination:switch', '.ut2-sw-w .ut2-sp-f', function (event, container, flag) {
if (flag) {
$(event.target).closest('.ut2-sw-w').siblings('.ut2-sp-n').removeClass('open');
}
});


$(document).on('ce:combination:switch', function (event, container, flag) {
let $combi_opener_title_btn = $(event.target);
let $combi_popup_dialog = container;
let $combi_outside_wrapper = $combi_popup_dialog.parent();
let combi_outside_wrapper_position_class;
let $combi_scrollable;
let combi_scrollable_selector;
let $combi_page_overlay = $combi_popup_dialog.siblings('.ui-widget-overlay');
const $combi_parent_dropdownbox = $combi_opener_title_btn.parent();
let combi_parent_dropdownbox__class_list;
const flag__combi_is_h_filters = $combi_opener_title_btn.is('.ty-horizontal-product-filters-dropdown__wrapper');
let $combi_adjacent_h_filters;
const flag__combi_is_catalog_v_menu = $combi_parent_dropdownbox.is('.top-menu-grid-vertical');
const flag__combi_is_fly_menu = $combi_opener_title_btn.is('.ut2-sp-n, .ut2-sp-f');
const flag__combi_is_filters_modal = $combi_parent_dropdownbox.is('.ut2-filters');
const flag__combi_is_categories_modal = $combi_parent_dropdownbox.is('.ut2-categories');
const flag__combi_has_outside_position = $combi_parent_dropdownbox.hasClass('cm-abt--ut2-move-bottom') || $combi_outside_wrapper.hasClass('ut2-dropdown-outside-position');
if (flag__combi_is_h_filters) {
$combi_adjacent_h_filters = $combi_parent_dropdownbox.siblings();
}
if (flag__combi_is_catalog_v_menu) {
$combi_scrollable = $combi_popup_dialog.find('.ut2-menu__list');
combi_scrollable_selector = '.ut2-menu__list';
}
if (flag__combi_is_fly_menu) {
$combi_opener_title_btn = container.siblings('.ut2-sp-n');
$combi_popup_dialog = container.siblings('.ut2-sw-w');
$combi_page_overlay = container;
$combi_scrollable = $combi_popup_dialog.find('.ut2-scroll');
combi_scrollable_selector = '.ut2-scroll';
}
if ($combi_parent_dropdownbox.hasClass('cm-abt--ut2-move-bottom')) {
if ($combi_parent_dropdownbox.is(':first-child')) {
combi_outside_wrapper_position_class = 'lt-position';
} else {
combi_outside_wrapper_position_class = 'rt-position';
}
if (!$combi_parent_dropdownbox.hasClass(combi_outside_wrapper_position_class)) {
$combi_parent_dropdownbox.addClass(combi_outside_wrapper_position_class);
}
combi_parent_dropdownbox__class_list = $combi_parent_dropdownbox[0].classList;
combi_parent_dropdownbox__class_list.remove('cm-abt--ut2-move-bottom', 'ty-dropdown-box', 'ty-dropdown-box2');
$('#tygh_main_container').append($combi_popup_dialog);
$combi_popup_dialog.wrap('<div class="ut2-dropdown-outside-position ' + combi_parent_dropdownbox__class_list + '"></div>').after($combi_page_overlay);
}
if (flag__combi_has_outside_position) {
$combi_outside_wrapper = $combi_popup_dialog.parent();
}
if (flag__combi_is_filters_modal) {
$combi_scrollable = $combi_popup_dialog.find('.ty-product-filters__wrapper');
combi_scrollable_selector = '.ty-product-filters__wrapper';
}
if (flag__combi_is_categories_modal) {
$combi_scrollable = $combi_popup_dialog.find('.ut2-subcategories');
combi_scrollable_selector = '.ut2-subcategories';
}
setTimeout(function () {
if ($combi_opener_title_btn.hasClass('open')) {
$combi_popup_dialog.addClass('js_open');
$combi_page_overlay.removeClass('hidden');
if (flag__combi_is_h_filters) {
if (_.abt__ut2.device === 'desktop') {
$combi_adjacent_h_filters.children('.ty-horizontal-product-filters-dropdown__wrapper.open').trigger('click');
}
fn__getDropdownPopupTopClearanceToWindowEdge($combi_opener_title_btn);
fn_sub__computeHfilterPopupSubtractedSpace($combi_popup_dialog);
}
if (flag__combi_is_catalog_v_menu || flag__combi_is_fly_menu || flag__combi_is_filters_modal || flag__combi_is_categories_modal) {
$('html').addClass('js_modal_-open');
}
if (flag__combi_is_catalog_v_menu || flag__combi_is_fly_menu) {
window.locked_page_scroll_abort_controller = new AbortController();
window.g_fn__offPageScroll(window.locked_page_scroll_abort_controller, $combi_scrollable.get(0), combi_scrollable_selector, $combi_popup_dialog.get(0));
}
if (flag__combi_is_catalog_v_menu) {
$('html').addClass('js_catalog-v-menu_-open');
fn__getDropdownPopupTopClearanceToWindowEdge($combi_opener_title_btn);
}
if (flag__combi_is_fly_menu) $('html').addClass('js_fly-menu_-open js_side-modal_-open');
if (flag__combi_has_outside_position && !$combi_popup_dialog.is('.js_forced-show')) {
$combi_popup_dialog.addClass('js_forced-show');
$combi_outside_wrapper.addClass('js_open');
$('html').addClass('js_side-modal_-open');
if (flag__combi_is_filters_modal) $('html').addClass('js_filters_modal_-open');
if (flag__combi_is_categories_modal) $('html').addClass('js_categories_modal_-open');
$combi_page_overlay.on('click.close_side_modal', function () {
if ($combi_opener_title_btn.hasClass('open')) {
$combi_opener_title_btn.trigger('click');
}
return false;
});
}
} else {
$combi_popup_dialog.removeClass('js_open');
$combi_page_overlay.addClass('hidden');
if (flag__combi_is_catalog_v_menu || flag__combi_is_fly_menu || flag__combi_is_filters_modal || flag__combi_is_categories_modal) {
$('html').removeClass('js_modal_-open');
}
if (flag__combi_is_catalog_v_menu) {
$('html').removeClass('js_catalog-v-menu_-open');
window.g_fn__offOffPageScroll(window.locked_page_scroll_abort_controller);
}
if (flag__combi_is_fly_menu) {
$combi_popup_dialog.on('transitionend', function () {
$(this).off('transitionend');
$('html').removeClass('js_fly-menu_-open js_side-modal_-open');
window.g_fn__offOffPageScroll(window.locked_page_scroll_abort_controller);
});
}
if (flag__combi_has_outside_position && $combi_popup_dialog.is('.js_forced-show')) {
$combi_outside_wrapper.removeClass('js_open').on('transitionend', function () {
$(this).off('transitionend');
$combi_popup_dialog.removeClass('js_forced-show');
$('html').removeClass('js_side-modal_-open');
if (flag__combi_is_filters_modal) $('html').removeClass('js_filters_modal_-open');
if (flag__combi_is_categories_modal) $('html').removeClass('js_categories_modal_-open');
$combi_page_overlay.off('click.close_side_modal');
});
}
}
}, 0);

if (container.is('.cm-smart-position-h:visible') || container.closest('.top-grid').length) {
const id = container.attr('id');
const isRtl = document.dir === 'rtl';
const dir = isRtl ? 'right' : 'left';
const within = container.parentsUntil('.container-fluid-row').last()
container.position({
within: within.get(0),
my: `${dir} top`,
at: `${dir} bottom`,
collision: 'fit',
of: $(`#sw_${id}, #on_${id}`).get(0)
});
const tipLeft = parseInt(container.css('left')) || 'auto';
container.css({
top: '',
'--tip-left': isRtl ? 'auto' : -1 * tipLeft + 'px',
'--tip-right': isRtl ? -1 * tipLeft + 'px' : 'auto',
});
}

});


window.g_fn__offOffPageScroll = function (locked_page_scroll_abort_controller) {
locked_page_scroll_abort_controller.abort();
$(window).off('.disable_scroll');
}
window.g_fn__offPageScroll = function (locked_page_scroll_abort_controller, el__scrollable_exception, scrollable_exception_selector, el__scrollable_exception_context) {
const flag__no_scrollable_exception = (typeof el__scrollable_exception === 'undefined' || el__scrollable_exception == null) ? true : false;
const scroll_keys = [33, 34, 35, 36, 38, 40];
let supports_passive_listener = false;
try {
window.addEventListener('test', null, Object.defineProperty({}, 'passive', {
get: function () {
supports_passive_listener = true;
}
}));
} catch (e) {
}
let listener_option;
let page_scroll_distance_top;
function preventMouseWheelAndTouchMoveScroll(event) {
if (flag__no_scrollable_exception || !$(event.target).closest(scrollable_exception_selector, el__scrollable_exception_context).length) {
event.preventDefault();
event.stopPropagation();
return false;
}
}
function preventKeyBoardScroll(event) {
if (scroll_keys.includes(event.keyCode)) {
event.preventDefault();
event.stopPropagation();
return false;
}
}
if (supports_passive_listener) {
listener_option = {
passive: false,
signal: locked_page_scroll_abort_controller.signal,
}
document.addEventListener('mousewheel', preventMouseWheelAndTouchMoveScroll, listener_option);
document.addEventListener('wheel', preventMouseWheelAndTouchMoveScroll, listener_option);
document.addEventListener('touchmove', preventMouseWheelAndTouchMoveScroll, listener_option);
document.addEventListener('keydown', preventKeyBoardScroll, listener_option);
}
page_scroll_distance_top = Math.round($(window).scrollTop());
$(window).on('scroll.disable_scroll', function (event) {
window.scrollTo({
top: page_scroll_distance_top,
behavior: 'instant'
});
event.preventDefault();
event.stopPropagation();
return false;
});
}


$(document).on('change', '.cm-product-filters-checkbox:enabled', (e) => {
const $filter_checkbox = $(e.target);
const filter_checkbox_id = $filter_checkbox.attr('id');
let $updated_product_filters;
let $updated_filter_checkbox;
let $updated_filter_checkbox__container;
let $updated_filter_checkbox__body;
let $core_products_found_notice;
let inteval_id;
let h_corrective = 10;
let $updated_filter_checkbox__popup;
$.ceEvent('one', 'ce.ajaxdone', function (elms, data, response_text) {
if (elms.length) {
$.each(elms, function (key, $elem) {
$updated_filter_checkbox = $elem.find('#' + filter_checkbox_id);
if ($updated_filter_checkbox.length) {
$updated_product_filters = $elem;
if ($updated_product_filters.closest('.ut2-dropdown-outside-position').length) {
h_corrective = -5;
}
if ($updated_product_filters.closest('.ut2-hz-filters').length) {
h_corrective = 15;
}
$updated_filter_checkbox__container = $updated_filter_checkbox.siblings('.ty-range-slider');
if ($updated_filter_checkbox__container.length < 1) $updated_filter_checkbox__container = $updated_filter_checkbox.closest('.cm-product-filters-checkbox-container');
$updated_filter_checkbox__body = $updated_filter_checkbox.parents('.ty-product-filters__block, .ty-product-filters, .ty-price-slider').last();
inteval_id = setInterval(function () {
$core_products_found_notice = $('.ty-tooltip--filter:visible');
if ($core_products_found_notice.length) {
clearInterval(inteval_id);
$core_products_found_notice.css({
"--ut2-changed-filter-checkbox-container-top": $updated_filter_checkbox__container.get(0).getBoundingClientRect().top + $updated_filter_checkbox__container.height() / 2 + "px",
"--ut2-changed-filter-checkbox-container-left": Tygh.language_direction === 'rtl' ? $(document).width() - $updated_filter_checkbox__body.get(0).getBoundingClientRect().left + h_corrective + "px" : $updated_filter_checkbox__body.get(0).getBoundingClientRect().right + h_corrective + "px"
});
}
}, 200);
setTimeout(function () {
clearInterval(inteval_id);
}, 2000);
$updated_filter_checkbox__popup = $updated_filter_checkbox.closest('.ty-horizontal-product-filters-dropdown__content');
if ($updated_filter_checkbox__popup.length) {
fn_sub__computeHfilterPopupSubtractedSpace($updated_filter_checkbox__popup);
}
return false;
}
});
}
});
if (_.abt__ut2.device !== 'desktop') {
$.ceEvent('on', 'ce.ajaxdone', (elements) => {
let needElem = $('.ty-horizontal-product-filters.cm-horizontal-filters.ut2-filters')
.find('#' + $(e.target).closest('.cm-horizontal-filters-content').attr('id'));
needElem.css('transition', 'unset').addClass('container-opened');
});
}
});


const fn_debounced__getDropdownPopupTopClearanceToWindowEdge = window.fn_utility__debounceUnderscore_fs(fn__getDropdownPopupTopClearanceToWindowEdge, 400);
function fn__getDropdownPopupTopClearanceToWindowEdge($dropdown_btn_head) {
if (window.matchMedia('(min-width: 768px)').matches) {
$dropdown_btn_head.each(function (i) {
const $dropdown_toggler = $(this);
if ($dropdown_toggler.is(':visible')) {
let $dropdown_popup = $dropdown_toggler.siblings('.ty-dropdown-box__content, .ty-horizontal-product-filters-dropdown__content');
if ($dropdown_popup.length) {
let $dropdown_popup__data_getter;
if ($dropdown_toggler.is('.ty-dropdown-box__title[id]')) {
$dropdown_popup__data_getter = $dropdown_popup.find('.ut2-menu');
} else if ($dropdown_toggler.is('.ty-horizontal-product-filters-dropdown__wrapper')) {
$dropdown_popup__data_getter = $dropdown_popup;
}
const $dropdown_popup__offset_parent = $dropdown_toggler.offsetParent();
if ($dropdown_popup__data_getter.length && $dropdown_popup__offset_parent.length) {
let dropdown_popup__position_top = $dropdown_popup.position().top ? $dropdown_popup.position().top : $dropdown_popup__offset_parent.outerHeight();
let rt__dropdown_popup__top_clearance;
if (window.g_fn_helper__checkIfElementIsFullyInViewport($dropdown_popup__offset_parent)) {
if ($dropdown_popup.is(':visible')) {
rt__dropdown_popup__top_clearance = $dropdown_popup.get(0).getBoundingClientRect().top;
} else {
rt__dropdown_popup__top_clearance = $dropdown_popup__offset_parent.get(0).getBoundingClientRect().top + dropdown_popup__position_top;
}
} else {
rt__dropdown_popup__top_clearance = $dropdown_popup__offset_parent.outerHeight();
}
rt__dropdown_popup__top_clearance = Math.ceil(rt__dropdown_popup__top_clearance);
$dropdown_popup__data_getter.css('--js_rt__dropdown-popup-_top-clearance-to-window-edge', `${rt__dropdown_popup__top_clearance}px`);
}
}
}
});
}
}


const fn_debounced__getHorizontalMenuPopupTopClearanceToWindowEdge = window.fn_utility__debounceUnderscore_fs(fn__getHorizontalMenuPopupTopClearanceToWindowEdge, 400);
function fn__getHorizontalMenuPopupTopClearanceToWindowEdge() {
$('.ut2-h__menu').each(function (i) {
const $h_menu = $(this);
if ($h_menu.is(':visible')) {
const $h_menu__top_link = $h_menu.find('.ut2-menu__link').filter(':visible').first();
if ($h_menu__top_link.length) {
const $h_menu__popup = $h_menu__top_link.siblings('.ut2-menu__submenu');
if ($h_menu__popup.length) {
const $h_menu__popup__offset_parent = $h_menu__top_link.offsetParent();
if ($h_menu__popup__offset_parent.length) {
let h_menu__popup__position_top = $h_menu__popup.position().top ? $h_menu__popup.position().top : $h_menu__popup__offset_parent.outerHeight();
let rt__h_menu__popup__top_clearance;
if (window.g_fn_helper__checkIfElementIsFullyInViewport($h_menu__popup__offset_parent)) {
if ($h_menu__popup.is(':visible')) {
rt__h_menu__popup__top_clearance = $h_menu__popup.get(0).getBoundingClientRect().top;
} else {
rt__h_menu__popup__top_clearance = $h_menu__popup__offset_parent.get(0).getBoundingClientRect().top + h_menu__popup__position_top;
}
} else {
rt__h_menu__popup__top_clearance = $h_menu__popup__offset_parent.outerHeight();
}
rt__h_menu__popup__top_clearance = Math.ceil(rt__h_menu__popup__top_clearance);
$h_menu.css("--js_rt__h-menu-popup-_top-clearance-to-window-edge", `${rt__h_menu__popup__top_clearance}px`);
}
}
}
}
});
}


window.g_fn_helper__checkIfElementIsFullyInViewport = function ($element) {
if ($element.offset().top >= $(window).scrollTop() && $element.offset().top + $element.outerHeight() < $(window).scrollTop() + $(window).height()) {
return true;
}
}


function fn__preventPageZoomingOnPinchAndDoubleTap() {
if (g_flag__is_iOS_device__alt) {
document.addEventListener('gesturestart', function (event) {
event.preventDefault();
}, {passive: false});
document.addEventListener('dblclick', function (event) {
event.preventDefault();
}, {passive: false});
$(document).on('touchend', function (event) {
if (event.touches) {
if (event.touches.length > 1) return;
}
const $elem = $(this);
let tap_time_delta;
let prior_tap_moment = $elem.data('prior_tap_moment');
if (prior_tap_moment) {
tap_time_delta = event.timeStamp - prior_tap_moment;
if (tap_time_delta < 500 && tap_time_delta > 0) {
event.preventDefault();
$(event.target).trigger('click');
}
}
$elem.data('prior_tap_moment', event.timeStamp);
});
}
}


function fn__keepTrackAjaxProcessThroughPreloaderDisplayProperty() {
let ajax_preloader_observer = new MutationObserver(function(mutationList, observer) {
mutationList.forEach(function(mutationRecord) {
if (mutationRecord.type === 'attributes' && mutationRecord.attributeName === 'style') {
if ($(mutationRecord.target).css('display') === 'block') {
$('body').addClass('js_ajax_-acts');
onStartAjaxProcess();
} else if ($(mutationRecord.target).css('display') === 'none') {
$('body').removeClass('js_ajax_-acts');
setTimeout(function () {
onCompletionAjaxProcess();
}, 500);
}
}
});
});
ajax_preloader_observer.observe(document.getElementById('ajax_loading_box'), {
attributes : true,
attributeFilter : ['style']
});
}
function onStartAjaxProcess() {
if (g_flag__is_firefox_android_browser) {
$('.ut2-sticky-panel__wrap').addClass('js_hidden');
}
}
function onCompletionAjaxProcess() {
if (g_flag__is_firefox_android_browser) {
$('.ut2-sticky-panel__wrap').removeClass('js_hidden');
}
}


window.g_fn__copySkuTextToClipboard = function ($copied) {
if ($copied.length > 0) {
const el__copied = $copied.get(0);
let cleaned_text;
cleaned_text = el__copied.textContent || el__copied.innerText || "";
cleaned_text = cleaned_text.replace(/\r?\n|\r/g, '').trim();
if (cleaned_text) {
if ('clipboard' in navigator) {
navigator.clipboard.writeText(cleaned_text).then(() => {
openCopySuccessedNotif();
}).catch((err) => {
copyTextViaExecCommand();
});
} else {
copyTextViaExecCommand();
}
} else {
openCopyFailedNotif();
}
function copyTextViaExecCommand() {
const $utility_temp_input = $('<input type="text" readonly style="position:fixed !important; inset-inline-start:-200vw !important; inset-block-start:-200vh !important; border:0 none; padding:unset; min-width:unset; width:1px !important; min-height:unset; height:1px !important;" />');
let flag__copy_status = false;
$('body').append($utility_temp_input);
$utility_temp_input.val(cleaned_text).select();
try {
if (document.execCommand("copy")) {
flag__copy_status = true;
} else {
flag__copy_status = false;
}
} catch (err) {
flag__copy_status = false;
}
$utility_temp_input.remove();
if (flag__copy_status) {
openCopySuccessedNotif();
} else {
openCopyFailedNotif();
}
}
} else {
openCopyFailedNotif();
}
function openCopySuccessedNotif() {
$.ceNotification('show', {
title: '',
message: '<div class="js-ins--notif-content-_sku-copied">' + _.tr('abt__ut2__sku_copy_status_success') + '<div>',
type: 'N',
message_state: 'I'
});
}
function openCopyFailedNotif() {
$.ceNotification('show', {
title: '',
message: '<div class="js-ins--notif-content-_sku-copied">' + _.tr('abt__ut2__sku_copy_status_fail') + '<div>',
type: 'E',
message_state: 'I'
});
}
}


function fn__toggleCheckoutCvv2NoteTtip() {
const $checkout_cvv2_note_mmq = window.matchMedia('(max-width:767px), (hover:none) and (pointer:coarse)');
let $checkout_cvv2_note_btn = {};
$checkout_cvv2_note_mmq.addListener(fn__checkoutCvv2NoteMmq_statusChanges);
function fn__checkoutCvv2NoteMmq_statusChanges(mmq) {
if (mmq.matches) {
$(document).on('click.checkout_toggle_cvv2_note', '.litecheckout__step-payments .ty-cvv2-about__title', function () {
$checkout_cvv2_note_btn = $(this);
if ($checkout_cvv2_note_btn.length > 0) {
$checkout_cvv2_note_btn.toggleClass('js_cvv2_-opened');
}
});
} else {
if ($checkout_cvv2_note_btn.length > 0) {
$checkout_cvv2_note_btn.removeClass('js_cvv2_-opened');
}
$(document).off('click.checkout_toggle_cvv2_note');
}
}
fn__checkoutCvv2NoteMmq_statusChanges($checkout_cvv2_note_mmq);
}


window.fn_utility__getDigitsAtStringEnd = function(str) {
let final_digits = str.match(/\d+$/);
return final_digits ? Number(final_digits[0]) : null;
}


function fn__unfoldEntireHeading() {
$(document).one('click.pb_heading', '.ut2-pb__title-wrap h1', function () {
$(this).addClass('js_unfold');
});
}


const toggleNoScroll = (flag) =>{
setTimeout(()=>{
let enableNoScroll = flag || $('.cm-abt--ut2-toggle-scroll[id^="off_"]:visible').length > 0;
if (enableNoScroll) {
$('body').data('scroll-top', window.pageYOffset);
}
$('html').toggleClass('no-scroll', enableNoScroll);
if (!enableNoScroll) {
let scrollTop = $('body').data('scroll-top');
window.scroll(0, scrollTop);
}
}, 0);
}

$(document).ready(function() {
$.extend(_.abt__ut2, {
functions: {
toggleNoScroll: toggleNoScroll,
in_array: function (val, arr) {
var answ = 0;
if (Array.isArray(arr)) {
answ = ~arr.indexOf(val);
} else {
answ = ~Object.keys(arr).indexOf(val);
}
return Boolean(answ);
},
detect_class_changes: function (elem, callback, add_old_val) {
var vanilla_elem = elem[0];
var observer = new MutationObserver(callback);
observer.observe(vanilla_elem, {
attributes: true,
attributeOldValue: add_old_val || false,
attributeFilter: ['class']
});
},
toggle_class_on_scrolling: function (element_to_manipulate, element_to_add_class, class_name, add_to_offset, conditions) {
var additional_offset = add_to_offset;
if (_.abt__ut2.settings.general.top_sticky_panel.enable[_.abt__ut2.device] === 'Y') {
additional_offset += $('.top-menu-grid').outerHeight();
}
$(window).on('scroll resize', function () {
var scroll_top = $(window).scrollTop() - additional_offset;
var scroll_bot = scroll_top + window.innerHeight;
var element_coords = element_to_manipulate.offset();
element_coords.bottom = element_coords.top + element_to_manipulate.outerHeight();
if (scroll_bot >= element_coords.bottom) {
if (conditions != void(0) && typeof conditions.add === 'function' && !conditions.add())
return false;
element_to_add_class.addClass(class_name);
} else {
if (conditions != void(0) && typeof conditions.remove === 'function' && !conditions.remove())
return false;
element_to_add_class.removeClass(class_name);
}
});
}
}
});
if (_.abt__ut2.settings.general.top_sticky_panel.enable[_.abt__ut2.device] === 'Y') {

$('body').data('ca-scroll-to-elm-offset', 70);
}
if (_.abt__ut2.controller === 'checkout' && _.abt__ut2.mode === 'cart') {
$('.ty-dropdown-box__title:not(.open)').addClass('__cart-page');
}
if (location.pathname !== '/'){
let menuItemClases = ':is(.ut2-menu__list, .ty-text-links)',
menuElemClases = '.ut2-menu__2nd-item__header, .ut2-menu__3rd-item, .ty-text-links__item',
activeElem = $(menuItemClases +' a[href$="'+location.pathname+'"]');
if(activeElem.length){
activeElem.parentsUntil(menuItemClases, menuElemClases).addClass('active');
activeElem.parentsUntil(menuItemClases,'.ut2-menu__2nd-item').find('.ut2-menu__2nd-item__header').addClass('active');
activeElem.parentsUntil(menuItemClases,'.ut2-menu__item').find('.ut2-menu__link').addClass('active');
}
}
});
$.ceEvent('on', 'ce.commoninit', function(context) {
let load_more_buttons = context.find('.load-more-btn');
if (load_more_buttons.length) {
$.getScript('js/addons/abt__unitheme2/components/block_load_more.js', function(){
load_more_buttons.each(function(){
this.onclick = function(){ window.ut2_load_products(this) };
});
});
}
let subscribeFooterForm = $('.ty-footer-form-block__input.cm-block-add-subscribe');
subscribeFooterForm.closest('form').on('submit', () => {
let emailErrorMessageF = $('.help-inline', subscribeFooterForm);
if (emailErrorMessageF.length) {
emailErrorMessageF.parent().append(emailErrorMessageF);
}
});
});
$.ceEvent('on', 'ce.abt__ut2_before_ajax_request', function(arguments){
let params = arguments[2];
if(params.method === 'get' &&
(
arguments[1].indexOf('block_manager.render') !== -1
||
arguments[1].indexOf('abt__ut2_load_blocks.get_block_content') !== -1
)
){
arguments[2].data = { ...arguments[2].data,
abt__ut2_initial_request:_.abt__ut2.request,
abt__ut2_assign_data:_.abt__ut2.assign_data
};
}
if(arguments[1].indexOf('geo_maps.shipping_estimation') !== -1){
arguments[2].pre_processing = function(data,params){
let product_id = params.data.product_id,
key = 'geo_maps_shipping_methods_list_' + product_id,
key_overload = key + '_overload .ty-geo-maps-shipping__popup';
if(data.html[key] !== undefined){
$('#' + key_overload)?.replaceWith(data.html[key]);
$.ceDialog('get_last')?.ceDialog('resize')
}
}
}
});
$(document).ready(function () {
if (document.documentElement.clientWidth > 768) {
var m = $('.hpo-menu');
if (m.length) {
var menu_height = m.height();
m.addClass('open-menu').find('.ty-dropdown-box__title:first').addClass('open');
var last_first_level_item = m.find('.ut2-menu__item:last-child');
var m_height = parseInt(last_first_level_item.offset().top + last_first_level_item.height());
var fixed_header = function() {
var scroll = $(window).scrollTop();
var top_panel = $('#tygh_main_container > .tygh-top-panel'),
top_panel_height = top_panel.height();
if (scroll >= m_height) {
$('.hpo-menu').removeClass('open-menu');
$('.hpo-menu > .ty-dropdown-box__title').removeClass('open');
} else {
$('body').removeClass('fixed-header').css('padding-top', '');
$('.hpo-menu').addClass('open-menu');
$('.hpo-menu > .ty-dropdown-box__title').addClass('open');
}
};
fixed_header();
$(window).scroll( fixed_header );
}
} else {
$('.ut2-menu__link[href="javascript:void(0)"]').click(function() {
var link = $(this);
var toggler = link.prev();
if (toggler.length && toggler.hasClass('ty-menu__item-toggle')) {
toggler.click();
}
});
}
(function() {
if (_.abt__ut2.settings.products.view.show_sticky_panel_add_to_cart[_.abt__ut2.device] === 'Y' && (_.abt__ut2.controller === 'products' && _.abt__ut2.mode === 'view') && $('.ty-product-block.sticky_add_to_cart').length) {
$('.menu-grid .ty-dropdown-box__title').on('click', function () {
var buttons = $('.ut2-pb__sticky_add_to_cart');
if (!buttons.hasClass('by_scroll')) {
buttons.toggleClass('hide_add_to_cart');
} else {
setTimeout(function () {
$(window).trigger('scroll');
}, 100);
}
});
}
})();
$('.ut2-pb.ut2-pb-mobile').on( "accordionbeforeactivate", function( event, { newHeader } ) {
newHeader?.parent().get(0).scrollIntoView({ behavior: 'instant', block: 'start' });
});
});
(function() {
var clicks_counter = 0;
$.ceEvent('on', 'dispatch_event_pre', function (e, jelm, processed) {
if (e.type === 'click') {


if (jelm.hasClass('cm-external-trigger') || jelm.parent('.cm-external-trigger').length) {
Array.from(document.getElementsByClassName('cm-external-triggered'), e =>{
document.getElementById('sw_'+e.closest('.cm-popup-box')?.id)?.click()
e.click();
}
);
}
}
});
}());
function ajaxDecorator(f) {
return function () {
let [method] = arguments;
if (method === 'request') {
$.ceEvent('trigger', 'ce.abt__ut2_before_ajax_request', [arguments]);
}
return f.apply(this, arguments);
}
}
$.ceAjax = ajaxDecorator($.ceAjax);
$.tools?.tooltip.addEffect('abtip', function(done){
const tip = this.getTip();
const conf = this.getConf();
if (_.abt__ut2.device !== 'desktop') {
toggleNoScroll(true);
if (!tip.has('.tooltip-content').length) {
tip.find('.tooltip-arrow').remove();
tip.removeClass('tooltip').addClass('ut2-tooltip')
.wrapInner(`<div class="tooltip-content"></div>`)
.wrapInner(`<div class="tooltip-wrap"></div>`)
.find('.tooltip-wrap')
.prepend($(`<span class="ut2-btn-close"><i class="ut2-icon-baseline-close esc" ></i></span>`)
.on('click', () => this.hide()));
}
}
tip.removeClass('hidden')
.fadeTo(conf.fadeInSpeed, conf.opacity, () => done())
.find('.tooltip-wrap')
.addClass('show');
}, function (done) {
if (_.abt__ut2.device !== 'desktop') {
toggleNoScroll(false);
this.getTip().addClass('hidden').find('.tooltip-wrap').removeClass('show');
}
this.getTip().hide();
done.call();
})
function tooltipDecorator(fn) {
return function () {
if (_.abt__ut2.settings.general.mobile_tooltip !== 'Y') return fn.apply(this, arguments)
if ((this.closest('[data-ca-dispatch],[href],.ab-hm-first-level-toggler').length) && _.abt__ut2.device !== 'desktop')
return fn.apply(this, [{
onShow() {
this.getTip().hide();
}
}, ...arguments]);
if (this.hasClass('ty-product-filters__tooltip')) return fn.apply(this, arguments);
if (!this.attr('id')?.startsWith('gdpr')) fn.apply(this, [{effect: 'abtip'}, ...arguments]);
if (_.abt__ut2.device === 'desktop') return fn.apply(this, arguments);
this.on('touchstart', function () {
const checkbox = $(this).find('input[type="checkbox"]');
checkbox.prop('checked', !checkbox.prop('checked'));
})
const $gdpr_tip = $(`#gdpr_tooltip_` + this.attr('id'));
const gdpr_tooltip = fn.apply(this, [{tip: $gdpr_tip.get(), effect: 'abtip'}], ...arguments);
$gdpr_tip.removeClass()
.addClass('ut2-tooltip hidden')
.children(':not(div)')
.remove();
$gdpr_tip.children()
.removeClass()
.addClass('tooltip-wrap')
.wrapInner(`<div class="tooltip-content"></div>`)
.prepend($(`<span class="ut2-btn-close"><i class="ut2-icon-baseline-close esc" ></i></span>`)
.on('click', () => this.data('tooltip').hide()));
return gdpr_tooltip;
}
}
$.fn.ceTooltip = tooltipDecorator($.fn.ceTooltip)
$(_.doc).on('ce:combination:switch', '.cm-abt--ut2-toggle-scroll', function (event, container, flag) {
let containerId = container[0].id;
container[0].classList.toggle('hidden', flag);
if (flag && event.target.id.startsWith('off_')){
const targetId = event.target.id.replace('off_','sw_')
_.doc.getElementById(targetId)?.classList.remove('open');
}
if (_.abt__ut2.device !== 'desktop') {
$(container).removeAttr('style');
(flag) ? $(container).removeClass('container-opened') : $(container).addClass('container-opened');
let filtersContainer = event.target.closest('.top-sticky-panel__filters');
if (filtersContainer) {
let {top} = filtersContainer.getBoundingClientRect();
let zIndex = !flag ? 1005 : '';
$('html').css('--mra-top', -top + 'px');
$(filtersContainer).css('z-index', zIndex);
}
}
toggleNoScroll(!flag);
return true;
});
$(_.doc).on('click', '.ty-horizontal-product-filters.cm-horizontal-filters.ut2-filters .ty-product-filters__reset-button', () => {
if (_.abt__ut2.device !== 'desktop') {
$('html').removeClass('no-scroll');
}
});
$.ceEvent('on', 'ce:geomap:location_set_after', function (location, $container, response, auto_detect) {
if (!response.is_detected || !$(_.body).find('.cm-warehouse-block-depends-by-location').length) {
return;
}
$('.ut2-sp-f:visible')?.click();
})
$(document).ready(function() {
$('.cm-dialog-opener.ut2-append-body').each(function () {
const $self = $(this);
const content = $(`#${$self.data('caTargetId')}`);
$self.parent().addClass('object-container');
content.length && content.appendTo($self);
});
});
$(window).on('scroll resize', () => {
$('.cm-tooltip,.ty-product-filters__tooltip').each(function (){
const tooltip = $(this).data('tooltip');
if (tooltip?.isShown()) {
tooltip.hide();
return false
}
});
});
$.ceEvent('on', 'ce.product_image_gallery.inner.beforeInit', function(ins) {
const list_type = ins.options.caProductList ?? undefined;
if (list_type) {
const type = _.abt__ut2.settings.product_list[list_type].show_gallery[_.abt__ut2.device];
ins.options.pagination = type === 'points';
ins.options.navigation = type === 'arrows';
}
})
}(Tygh, Tygh.$));
