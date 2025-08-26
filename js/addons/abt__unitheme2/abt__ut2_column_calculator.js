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
let prev_doc_width = 0;
const fn_debounced__catalogSubmenuNotCascading2ndListColumnFilling = window.g_fn__debounceUnderscore_fs(fn__catalogSubmenuNotCascading2ndListColumnFilling, 200);
function fn__catalogSubmenuNotCascading2ndListColumnFilling(flag__force) {
const one_col_width = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--menu-col-min-width'));
let new_doc_width = $(document).width();
if ((new_doc_width === prev_doc_width && flag__force !== true) || window.matchMedia('(max-width: 767px)').matches) {
return true;
}
prev_doc_width = new_doc_width;
$('.ut2-menu').each(function () {
const $menu = $(this);
const $menu_inbox = $menu.children('.ut2-menu__inbox');
const $menu_list = $menu.find('.ut2-menu__list')
const setting_cols = parseInt(getComputedStyle($menu_list.get(0)).getPropertyValue('--menu-columns'));
const flag__is_compact = $menu_list.closest('.compact').length >= 1 && $menu.closest('.menu-1level').length < 1;
const flag__is_vertical = $menu.is('.ut2-v__menu');
const top_level_column_width = flag__is_vertical ? parseInt(getComputedStyle(document.documentElement).getPropertyValue('--menu-main-width')) + 1 : 0;
$menu_list.find('.ut2-menu__item').not('.item-1st-no-drop').each(function () {
const item_1st_lvl = $(this);
const $submenu_carrier = item_1st_lvl.find('.ut2-menu__submenu__carrier').not('.cascading').not('.row-filling');
if ($submenu_carrier.length) {
const subitems_count = item_1st_lvl.data('subitemsCount');
const $submenu_list_wrapper = $submenu_carrier.children('.ut2-menu__submenu__wrapper');
const $submenu_list = $submenu_list_wrapper.children('.ut2-menu__2nd-list');
const submenu_list_gap = parseInt(getComputedStyle($submenu_list.get(0)).getPropertyValue('gap'));
const $submenu_cols = $submenu_list.children('.ut2-menu__2nd-col');
let submenu_list_width;
let drodown_width;
let fitting_cols;
$submenu_list.data('fitting_cols', 0);
if ((!flag__is_vertical && $submenu_list.is(':visible')) || (flag__is_vertical && $submenu_list.is(':visible') && $menu_inbox.length && $menu_inbox.width() > top_level_column_width)) {
submenu_list_width = $submenu_list.width();
} else {
if (flag__is_compact && window.matchMedia('(min-width: 1024px)').matches) {
drodown_width = top_level_column_width * 3;
} else if (!flag__is_compact || (flag__is_compact && window.matchMedia('(max-width: 1023px)').matches)) {
drodown_width = $submenu_list.closest('.container-fluid-row').children('.row-fluid').width();
}
submenu_list_width =
drodown_width - top_level_column_width -
parseInt(getComputedStyle($submenu_carrier.get(0)).getPropertyValue('padding-inline-end')) -
parseInt(getComputedStyle($submenu_list_wrapper.get(0)).getPropertyValue('padding-inline-end')) -
parseInt(getComputedStyle($submenu_list_wrapper.get(0)).getPropertyValue('padding-inline-start'));
}

fitting_cols = Math.floor((submenu_list_width + submenu_list_gap) / (one_col_width + submenu_list_gap));
if (setting_cols >= fitting_cols) {
if ($submenu_list.data('fitting_cols') !== fitting_cols) {
$submenu_list.data('fitting_cols', fitting_cols);
if ($submenu_cols.length >= fitting_cols) {
let items_in_cols = Math.ceil(subitems_count / fitting_cols);
let big_cols_count = subitems_count % fitting_cols;
let index = 0;
for (let i = 0; i < fitting_cols; i++) {
if (i === big_cols_count && big_cols_count !== 0) {
items_in_cols--;
}
let $column = $submenu_cols.eq(i);
for (let j = 0; j < items_in_cols; j++) {
let $li = $submenu_list.find('[data-elem-index=' + index++ + ']');
$li.appendTo($column);
}
}
}
}
}
}
});
});
}
$.ceEvent('on', 'ce.ut2-hm-toggle', function(btn, parent, elem, is_prev){
fn__catalogSubmenuNotCascading2ndListColumnFilling(true);
});
$(window).on('resize.menu_column_filling', fn_debounced__catalogSubmenuNotCascading2ndListColumnFilling);
}(Tygh, Tygh.$));