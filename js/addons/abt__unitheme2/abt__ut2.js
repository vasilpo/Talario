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
if (user_agent.includes("Firefox") || user_agent.includes("FxiOS")) {
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
} else if (user_agent.includes("Safari") && /Apple/.test(user_agent)) {
return "Apple Safari";
} else {
return "unknown";
}
}
const g__browser_name = window.g_fn__getBrowserName(navigator.userAgent);
const g_flag__is_firefox_android_browser = /^Linux a/.test(navigator.oscpu);
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
const g__ut2_top_panel_height = parseInt($(':root').css('--ut2-top-panel-height'));
const g__ut2_header_1st_row_height = parseInt($(':root').css('--ut2-header-height'));
const g__ut2_header_2nd_row_height_1 = parseInt($(':root').css('--ut2-header__2nd-row-_height-_1'));
const g__ut2_header_2nd_row_height_2 = parseInt($(':root').css('--ut2-header__2nd-row-_height-_2'));
let prior_page_scrolling_distance = 0;
(function(_, $) {

window.g_fn__debounceUnderscore_fs = function (func, wait, immediate) {
let timeout;
return function executedFunction() {
const context = this;
const args = arguments;
const later = function() {
timeout = null;
if (!immediate) func.apply(context, args);
};
const callNow = immediate && !timeout;
clearTimeout(timeout);
timeout = setTimeout(later, wait);
if (callNow) func.apply(context, args);
};
}


$(window).on('load.global', function (event) {
$('body').addClass('js_window_-loaded');
});


$(document).ready(function () {
if (g_flag__is_iOS_device__alt) {
$('body').addClass('js_ios');
}
window.g_mmq__mobile.addListener(fn__mmqMobile_statusChanges);
function fn__mmqMobile_statusChanges(mmq) {
if (mmq.matches) {
$(document).on('click.toggle_cvv2_info', '.ty-cvv2-about__title', toggleCvv2Info);
} else {
if ($('.ty-cvv2-about__title').length > 0) {
$('.ty-cvv2-about__title').removeClass('js-state--payment-card-cvv2_-opened');
}
$(document).off('click.toggle_cvv2_info');
}
}
fn__mmqMobile_statusChanges(window.g_mmq__mobile);
fn__preventPageZoomingOnPinchAndDoubleTap();
fn__keepTrackAjaxProcessThroughPreloaderDisplayProperty();
fn__catalogMenu();
fn__headerAndPanels();
fn__fixStickySidebar();
$(window).on('scroll.global', function (event) {
fn_debounced__getVerticalMenuGapWindowTopEdge($('.top-menu-grid-vetrtical.ty-dropdown-box .ty-dropdown-box__title[id]'));
fn_debounced__getHorizontalMenuGapWindowTopEdge();
});
$(window).on('resize.global', function (event) {
fn_abt__ut2_calc_cell($(document));
fn_debounced__fixStickySidebar();
fn_debounced__getVerticalMenuGapWindowTopEdge($('.top-menu-grid-vetrtical.ty-dropdown-box .ty-dropdown-box__title[id]'));
fn_debounced__getHorizontalMenuGapWindowTopEdge();
});
$(document).on('click.global', function(event) {
let $click_target = $(event.target);
if (_.abt__ut2.device === 'desktop' && window.matchMedia('(max-width: 775px)').matches) {
if (! $click_target.closest('.ut2__horizontal-product-filters-dropdown').length ) {
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


function fn__headerAndPanels() {
const setting__enable_sticky_header = _.abt__ut2.settings.general.top_sticky_panel.enable[_.abt__ut2.device] === 'Y';
const setting__enable_sticky_fc_panel = _.abt__ut2.settings.category.show_sticky_panel_filters_and_categories[_.abt__ut2.device] === 'Y';
const setting__show_sticky_buy_panel = _.abt__ut2.settings.products.view.show_sticky_panel_add_to_cart[_.abt__ut2.device];
const setting__show_sticky_bottom_panel = _.abt__ut2.settings.general.sticky_panel.enable_sticky_panel[_.abt__ut2.device] === 'Y';
const body_class_header_stuck = 'fixed-header';
const body_class_whole_header_out_of_viewport = 'js_whole-header-out-of-viewport';
const body_class_fc_panel_stuck = 'sticky-fc-panel';
const body_class_buy_panel_stuck = 'sticky-add-to-cart';
const html_class_buy_panel_stuck_top = 'js_sticky-buy-panel-_top';
const body_class_bottom_panel_stuck = 'sticky-panel';
const mmq__header_search_2nd_row_mode = window.matchMedia('(max-width: 1140px)');
const $top_panel = $('.tygh-top-panel');
const top_panel_height = ($top_panel.length > 0 && $top_panel.find('.container-fluid-row').length > 0) ? Math.round($top_panel.outerHeight()) : 0;
const $tygh_header = $('.tygh-header');
const $header_grid = $tygh_header.children('.container-fluid, .container');
const $header_allows_h_menu_stuck_top_sentinel = $('.ut2-header-allows-h-menu-stuck-top-sentinel');
const flag__is_header_advanced = $header_grid.is('.advanced-header');
const flag__is_header_default = $header_grid.is('.default-header');
const flag__is_header_light = $header_grid.is('.light-header');
const flag__is_header_light_v1 = $header_grid.is('.light-header:not(.v2):not(.v3)');
const flag__is_header_light_v2 = $header_grid.is('.v2');
const flag__is_header_light_v3 = $header_grid.is('.v3');
const flag__is_header_allows_h_menu = flag__is_header_default || flag__is_header_light_v2;
const flag__is_header_checkout = $header_grid.is('.litecheckout__header');
const flag__is_header_allows_only_1st_row = flag__is_header_light_v1 || flag__is_header_light_v3 || flag__is_header_checkout;
const $header_1st_row = $header_grid.children('.container-fluid-row:not(.top-menu-grid)');
let header_1st_row_height;
const $header_2nd_row = $header_grid.children('.container-fluid-row.top-menu-grid');
let header_2nd_row_height;
const $header_stuck_row = flag__is_header_advanced ? $header_2nd_row : $header_1st_row;
let header_stuck_height;
const $fc_panel = $('.top-sticky-panel__filters');
const $buy_panel = $('#ut2_pb__sticky_add_to_cart');
const $buy_panel_stuck_sentinel = $('.ut2-pb__button.ty-product-block__button .ty-btn__add-to-cart');
const $bottom_panels_wrapper = $('.ut2-sticky-panel__wrap');
function getHeader1stRowHeight() {
return ($header_1st_row.length > 0) ? Math.round($header_1st_row.outerHeight()) : 0;
}
header_1st_row_height = getHeader1stRowHeight();
function pushToCSSheader1stRowHeight() {
$(':root').css('--header-height', `${header_1st_row_height}px`);
}
pushToCSSheader1stRowHeight();
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
if ($header_stuck_row.length > 0) {
if (flag__is_header_advanced) {
header_stuck_height = header_2nd_row_height;
} else if (flag__is_header_default) {
if (mmq__header_search_2nd_row_mode.matches) {
header_stuck_height = header_1st_row_height + g__ut2_header_2nd_row_height_1;
} else {
header_stuck_height = header_1st_row_height;
}
} else {
header_stuck_height = header_1st_row_height;
}
} else {
header_stuck_height = 0;
}
$(':root').css({
'--menu-height': `${header_2nd_row_height}px`,
'--top-panel-height': `${top_panel_height}px`,
});
if (flag__is_header_advanced) $tygh_header.addClass('advanced_header');
if (flag__is_header_default) $tygh_header.addClass('default_header');
if (flag__is_header_light) $tygh_header.addClass('light_header');
if (flag__is_header_light_v1) $tygh_header.addClass('v1');
if (flag__is_header_light_v2) $tygh_header.addClass('v2');
if (flag__is_header_light_v3) $tygh_header.addClass('v3');
if (flag__is_header_allows_h_menu) $tygh_header.addClass('allows-h-menu');
if (flag__is_header_checkout) $tygh_header.addClass('checkout_header');
if (flag__is_header_allows_only_1st_row) $tygh_header.addClass('allows-only-1st-row');
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
let fn__buyPanelIntsecObsCB;
let buyPanelIntsecObsOpt;
let buyPanelIntsecObserver;
if (setting__enable_sticky_fc_panel && $fc_panel.length > 0) {
fn__fcPanelIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
const $observed_target = $(entry.target);
$('body').toggleClass(body_class_fc_panel_stuck, !entry.isIntersecting);
});
}
fn__fcPanelIntsecObsOpt = {
root: null,
rootMargin: `${(setting__enable_sticky_header ? header_stuck_height + 1 : 1) * -1}px 0px 0px`,
threshold: [1],
}
fcPanelIntsecObserver = new IntersectionObserver(fn__fcPanelIntsecObsCB, fn__fcPanelIntsecObsOpt);
if (!flag__is_header_default) {
fcPanelIntsecObserver.observe($fc_panel.get(0));
} else {
mmq__header_search_2nd_row_mode.addListener(fn__headerSearchModeChanges);
function fn__headerSearchModeChanges(mmq) {
fcPanelIntsecObserver.disconnect();
if (mmq.matches) {
header_stuck_height = getHeader1stRowHeight() + g__ut2_header_2nd_row_height_1;
} else {
header_stuck_height = getHeader1stRowHeight();
}
fn__fcPanelIntsecObsOpt.rootMargin = `${(setting__enable_sticky_header ? header_stuck_height + 1 : 1) * -1}px 0px 0px`;
fcPanelIntsecObserver = new IntersectionObserver(fn__fcPanelIntsecObsCB, fn__fcPanelIntsecObsOpt);
fcPanelIntsecObserver.observe($fc_panel.get(0));
}
fn__headerSearchModeChanges(mmq__header_search_2nd_row_mode);
}
}
if (setting__enable_sticky_header && $header_stuck_row.length > 0) {
if (flag__is_header_advanced) {
fn__headerAdvancedIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
$('body').toggleClass(body_class_header_stuck, !entry.isIntersecting);
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
fn__headerAllowsOnly1stRowIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
$('body').toggleClass(body_class_header_stuck, !entry.isIntersecting);
});
}
headerAllowsOnly1stRowIntsecObsOpt = {
root: null,
rootMargin: '-1px 0px 0px',
threshold: [1],
}
headerAllowsOnly1stRowIntsecObserver = new IntersectionObserver(fn__headerAllowsOnly1stRowIntsecObsCB, headerAllowsOnly1stRowIntsecObsOpt);
headerAllowsOnly1stRowIntsecObserver.observe($header_stuck_row.get(0));
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
let realtime_width = Math.floor(entry.borderBoxSize[0].inlineSize);
let realtime_height = Math.floor(entry.borderBoxSize[0].blockSize);
if (header_1st_row_height != realtime_height) {
header_1st_row_height = ($header_1st_row.length > 0) ? realtime_height : 0;
pushToCSSheader1stRowHeight();
if (flag__is_header_default) {
if (mmq__header_search_2nd_row_mode.matches) {
header_stuck_height = header_1st_row_height + g__ut2_header_2nd_row_height_1;
} else {
header_stuck_height = header_1st_row_height;
}
} else if (!flag__is_header_advanced) {
header_stuck_height = header_1st_row_height;
}
if (setting__enable_sticky_fc_panel && $fc_panel.length > 0) {
fcPanelIntsecObserver.disconnect();
fn__fcPanelIntsecObsOpt.rootMargin = `${(setting__enable_sticky_header ? header_stuck_height + 1 : 1) * -1}px 0px 0px`;
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
if (setting__show_sticky_buy_panel !== 'none' && $buy_panel.length > 0 && $buy_panel_stuck_sentinel.length > 0) {
if (!$.contains($bottom_panels_wrapper.get(0), $buy_panel.get(0))) {
$buy_panel.prependTo($bottom_panels_wrapper).show();
}
if (setting__show_sticky_buy_panel === 'top') {
$('html').addClass(html_class_buy_panel_stuck_top);
}
fn__buyPanelIntsecObsCB = (entryList, obsInstance) => {
entryList.forEach((entry) => {
$('body').toggleClass(body_class_buy_panel_stuck, !entry.isIntersecting);
});
}
buyPanelIntsecObsOpt = {
root: null,
rootMargin: '-1px 0px -1px',
threshold: [0],
}
buyPanelIntsecObserver = new IntersectionObserver(fn__buyPanelIntsecObsCB, buyPanelIntsecObsOpt);
buyPanelIntsecObserver.observe($buy_panel_stuck_sentinel.get(0));
}
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
let {inlineSize, blockSize} = entry.borderBoxSize[0];
const $observed_this_menu = $(entry.target);
let this_menu__top_level_width_realtime = Math.floor(inlineSize);
$this_menu.css("--js_menu-v-top-level-_width_-realtime", this_menu__top_level_width_realtime + "px");
}
}, 400);
});
resizeObserver.observe($this_menu.get(0));
}
}
});
$(document).on('ce:combination:switch', '.top-menu-grid-vetrtical.ty-dropdown-box .ty-dropdown-box__title[id]', function (event, container, flag) {
if (flag === false) {
const $v_menu__toggler = $(this);
fn_debounced__getVerticalMenuGapWindowTopEdge($v_menu__toggler);
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


const fn_debounced__keepTrackPageScrollingDirection = window.g_fn__debounceUnderscore_fs(fn__keepTrackPageScrollingDirection, 400);
function fn__keepTrackPageScrollingDirection() {
let current_page_scrolling_distance = $(document).scrollTop();
if (current_page_scrolling_distance > prior_page_scrolling_distance) {
$('body').removeClass('js_page_-scrolled-_up js_page_-scrolled-_none').addClass('js_page_-scrolled-_down');
}
if (current_page_scrolling_distance < prior_page_scrolling_distance) {
$('body').removeClass('js_page_-scrolled-_down js_page_-scrolled-_none').addClass('js_page_-scrolled-_up');
}
if (current_page_scrolling_distance == prior_page_scrolling_distance) {
$('body').removeClass('js_page_-scrolled-_down js_page_-scrolled-_up').addClass('js_page_-scrolled-_none');
}
prior_page_scrolling_distance = current_page_scrolling_distance;
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


const fn_debounced__getVerticalMenuGapWindowTopEdge = window.g_fn__debounceUnderscore_fs(fn__getVerticalMenuGapWindowTopEdge, 400);
function fn__getVerticalMenuGapWindowTopEdge($v_menu__toggler) {
if (window.matchMedia('(min-width: 768px)').matches) {
$v_menu__toggler.each(function (i) {
let $this_v_menu__toggler = $(this);
if ($this_v_menu__toggler.is(':visible')) {
let $this_v_menu__dropdown = $this_v_menu__toggler.siblings('.ty-dropdown-box__content');
if ($this_v_menu__dropdown.length) {
let $this_v_menu__dropdown__data_getter = $this_v_menu__dropdown.find('.ut2-menu');
let $this_v_menu__dropdown__offset_parent = $v_menu__toggler.offsetParent();
if ($this_v_menu__dropdown__data_getter.length && $this_v_menu__dropdown__offset_parent.length) {
let this_v_menu__dropdown__position_top = $this_v_menu__dropdown.position().top ? $this_v_menu__dropdown.position().top : $this_v_menu__dropdown__offset_parent.outerHeight();
let this_v_menu__dropdown__top_gap;
if ($this_v_menu__dropdown.is(':visible')) {
if (_.abt__ut2.settings.general.top_sticky_panel.enable[_.abt__ut2.device] === 'Y') {
this_v_menu__dropdown__top_gap = $this_v_menu__dropdown.get(0).getBoundingClientRect().top;
} else {
this_v_menu__dropdown__top_gap = $this_v_menu__dropdown.offset().top;
}
} else {
if (_.abt__ut2.settings.general.top_sticky_panel.enable[_.abt__ut2.device] === 'Y') {
this_v_menu__dropdown__top_gap = $this_v_menu__dropdown__offset_parent.get(0).getBoundingClientRect().top + this_v_menu__dropdown__position_top;
} else {
this_v_menu__dropdown__top_gap = $this_v_menu__dropdown__offset_parent.offset().top + this_v_menu__dropdown__position_top;
}
}
this_v_menu__dropdown__top_gap = Math.ceil(this_v_menu__dropdown__top_gap);
$this_v_menu__dropdown__data_getter.css("--ut2-v-menu-dropdown-gap-window-top-edge", this_v_menu__dropdown__top_gap + "px");
}
}
}
});
}
}


const fn_debounced__getHorizontalMenuGapWindowTopEdge = window.g_fn__debounceUnderscore_fs(fn__getHorizontalMenuGapWindowTopEdge, 400);
function fn__getHorizontalMenuGapWindowTopEdge() {
$('.ut2-h__menu').each(function () {
let $this_h_menu = $(this);
if ($this_h_menu.is(':visible')) {
let $this_h_menu__top_link = $this_h_menu.find('.ut2-menu__link').filter(':visible').first();
if ($this_h_menu__top_link.length) {
let $this_h_menu__dropdown = $this_h_menu__top_link.siblings('.ut2-menu__submenu');
if ($this_h_menu__dropdown.length) {
let $this_h_menu__dropdown__offset_parent = $this_h_menu__top_link.offsetParent();
if ($this_h_menu__dropdown__offset_parent.length) {
let this_h_menu__dropdown__position_top = $this_h_menu__dropdown.position().top ? $this_h_menu__dropdown.position().top : $this_h_menu__dropdown__offset_parent.outerHeight();
let this_h_menu__dropdown__top_gap = $this_h_menu__dropdown__offset_parent.get(0).getBoundingClientRect().top + this_h_menu__dropdown__position_top;
if ($this_h_menu__dropdown.is(':visible')) {
this_h_menu__dropdown__top_gap = $this_h_menu__dropdown.get(0).getBoundingClientRect().top;
} else {
this_h_menu__dropdown__top_gap = $this_h_menu__dropdown__offset_parent.get(0).getBoundingClientRect().top + this_h_menu__dropdown__position_top;
}
this_h_menu__dropdown__top_gap = Math.ceil(this_h_menu__dropdown__top_gap);
$this_h_menu.css("--ut2-h-menu-dropdpwn-gap-window-top-edge", this_h_menu__dropdown__top_gap + "px");
}
}
}
}
});
}


delayingHorizontalMenuDropdownOpening();
function delayingHorizontalMenuDropdownOpening() {
if (window.matchMedia('(min-width: 768px) and (hover: hover)').matches) {
$('.ut2-h__menu').each(function () {
const $this_h_menu = $(this);
const $this_h_menu__1st_items = $this_h_menu.find('.ut2-menu__item').not('.item-1st-no-drop');
const open_delay = 300;
$this_h_menu__1st_items.each(function () {
const $this_h_menu__this_1st_item = $(this);
const $this_h_menu__this_1st_submenu = $this_h_menu__this_1st_item.find('.ut2-menu__submenu__carrier');
let open_timer_id;
let close_timer_id;
let flag__open_state = false;
if ($this_h_menu__this_1st_submenu.length) {
$this_h_menu__this_1st_item.on('mouseenter', function () {
clearTimeout(close_timer_id);
if (!flag__open_state) {
open_timer_id = setTimeout(function () {
$this_h_menu__this_1st_item.addClass('js_open');
flag__open_state = true;
}, open_delay - 1);
}
}).on('mouseleave', function () {
clearTimeout(open_timer_id);
if (flag__open_state) {
close_timer_id = setTimeout(function () {
$this_h_menu__this_1st_item.removeClass('js_open');
flag__open_state = false;
}, open_delay);
}
});
}
});
});
}
}


const fn_debounced__fixStickySidebar = window.g_fn__debounceUnderscore_fs(fn__fixStickySidebar, 400);
function fn__fixStickySidebar() {
let el__product_card_sidebar = document.querySelector('.ut2-pb__right, .ut2-pb__right-wrapper');
if (_.abt__ut2.device !== 'mobile' && !window.g_mmq__mobile.matches && $(el__product_card_sidebar).length) {
let product_card_sidebar_height = $(el__product_card_sidebar).outerHeight();
let product_card_sidebar_sticky_top = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--gap-s'));
let $header = $('#tygh_main_container > .tygh-header > .header-grid');
let sticky_header_elem_height = 0;
let $spt_sticky_nav = $('.ab-spt-floating-position-after_h1');
let spt_sticky_nav_height = 0;
if ($('html').hasClass('sticky-top-panel')) {
if ($header.is('.advanced-header')) {
sticky_header_elem_height = $header.children('.top-menu-grid').outerHeight();
} else if ($header.is('.default-header')) {
sticky_header_elem_height = $header.children('.container-fluid-row:first-child').outerHeight();
} else if ($header.is('.light-header')) {
sticky_header_elem_height = $header.find('.top-menu-grid:not(.second-header-grid)').outerHeight();
}
product_card_sidebar_sticky_top = product_card_sidebar_sticky_top + sticky_header_elem_height;
}
if ($spt_sticky_nav.length) {
spt_sticky_nav_height = $spt_sticky_nav.outerHeight();
product_card_sidebar_sticky_top = product_card_sidebar_sticky_top + spt_sticky_nav_height;
}
if (product_card_sidebar_height + product_card_sidebar_sticky_top > document.documentElement.clientHeight) {
$(el__product_card_sidebar).addClass('js-mode--sticky-off');
$(el__product_card_sidebar).css("top", "unset");
} else {
$(el__product_card_sidebar).removeClass('js-mode--sticky-off');
$(el__product_card_sidebar).css("top", product_card_sidebar_sticky_top + 'px');
}
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


function toggleCvv2Info() {
$payment_card_cvv2_btn = $(this);
if ($payment_card_cvv2_btn.length > 0) {
$payment_card_cvv2_btn.toggleClass('js-state--payment-card-cvv2_-opened', !$payment_card_cvv2_btn.hasClass('js-state--payment-card-cvv2_-opened'));
}
}


const toggleNoScroll = (flag) =>{
setTimeout(()=>{
let enableNoScroll = flag || $('.cm-abt--ut2-toggle-scroll[id^="off_"]:visible').length > 0;
if (enableNoScroll) {
$('body').data('scroll-top', window.pageYOffset);
}
$('html').toggleClass('no-scroll', enableNoScroll);
if ($('.ut2-sp-n').hasClass('open') && enableNoScroll) {
$('body').addClass('fly-menu');
} else {
$('body').removeClass('fly-menu');
}
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
$(_.doc).on('ce:combination:switch', function(event, container, flag){
let p = container.parent();
if(p.hasClass('cm-abt--ut2-move-bottom')){
let c = p[0].classList;
c.remove('cm-abt--ut2-move-bottom', 'ty-dropdown-box', 'ty-dropdown-box2');
$('#tygh_main_container').append(container);
container.wrap("<div class='ut2-dropdown-outside-position " + c +"'></div>");
}
if (container.is('.cm-smart-position-h:visible') || container.closest('.top-grid').length) {
const id = container.attr('id');
const isRtl = document.dir === 'rtl';
const dir = isRtl ? 'right' : 'left';
const within = container.parentsUntil('.container-fluid-row').last()
container.position({
within:within.get(0),
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
})
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
$(_.doc).on('ce:combination:switch', '.ty-horizontal-product-filters-dropdown__wrapper', function (event, container, flag) {
if (_.abt__ut2.device === 'desktop' && window.matchMedia('(max-width: 775px)').matches) {
let $this_head_filter = $(this);
let $this_popup_filter = container;
let $this_filter = $this_head_filter.parent('.ut2__horizontal-product-filters-dropdown');
let $other_filters = $this_filter.siblings();
if (!flag) {
$other_filters.children('.ty-horizontal-product-filters-dropdown__wrapper.open').trigger('click');
}
$this_popup_filter.toggleClass('hidden', flag);
}
});
$(_.doc).on('change', '.cm-product-filters-checkbox:enabled', (e) => {
const $filter_checkbox = $(e.target);
const field_id = $filter_checkbox.attr('id');
let $product_filters;
let $updated_filter_checkbox;
let $filter_checkbox__container;
let $filter_body;
let $core_products_found_notice;
let $own_products_found_notice;
let inteval_id;
let h_corrective = 10;
$.ceEvent('one', 'ce.ajaxdone', function (elms, data, response_text) {
if (elms.length) {
$.each(elms, function (key, $elem) {
$updated_filter_checkbox = $elem.find('#' + field_id);
if ($updated_filter_checkbox.length) {
$product_filters = $elem;
if ($product_filters.closest('.ut2-dropdown-outside-position').length) {
h_corrective = -5;
}
if ($product_filters.closest('.ut2-hz-filters').length) {
h_corrective = 15;
}
$filter_checkbox__container = $updated_filter_checkbox.closest('.cm-product-filters-checkbox-container');
$filter_body = $updated_filter_checkbox.parents('.ty-product-filters__block, .ty-product-filters').last();
inteval_id = setInterval(function () {
$core_products_found_notice = $('.ty-tooltip--filter:visible');
if ($core_products_found_notice.length) {
clearInterval(inteval_id);

$core_products_found_notice.css({
"--ut2-changed-filter-checkbox-container-top": $filter_checkbox__container.get(0).getBoundingClientRect().top + $filter_checkbox__container.height() / 2 + "px",
"--ut2-changed-filter-checkbox-container-left": Tygh.language_direction === 'rtl' ? $(document).width() - $filter_body.get(0).getBoundingClientRect().left + h_corrective + "px" : $filter_body.get(0).getBoundingClientRect().right + h_corrective + "px"
});
}
}, 200);
setTimeout(function () {
clearInterval(inteval_id);
}, 2000);
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
