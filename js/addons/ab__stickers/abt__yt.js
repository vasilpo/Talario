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
$.ceEvent('on', 'ab__stickers.stickers_loaded', function (context, data, params, items) {
const grid_wrappers = items.parents('.grid-list');
grid_wrappers.each(function (i, grid_wrapper) {
grid_wrapper = $(grid_wrapper);
if (grid_wrapper.length) {
const calculate_list_size = function () {
let max_stickers_containers_height = 0;
grid_wrapper.find('.ty-grid-list__item').each(function (i, item) {
item = $(item);
let stickers_containers_height = 0;
item.find('.ab-stickers-container__price_before, .ab-stickers-container__price_after').each(function (i, container) {
container = $(container);
stickers_containers_height += container.outerHeight(true);
});
max_stickers_containers_height = Math.max(stickers_containers_height, max_stickers_containers_height);
});
if (max_stickers_containers_height) {
grid_wrapper.find('.ty-grid-list__item').each(function (i, item) {
item = $(item);
let item_yet_added_height = item.data('ab--stickers-height') ?? 0;
if (max_stickers_containers_height > item_yet_added_height) {
const add_height_for_item = max_stickers_containers_height - item_yet_added_height;
let item_height = item.height() + add_height_for_item;
item.data('ab--stickers-height', max_stickers_containers_height);
if ($(window).width() < 768) {
item.get(0).style.setProperty('height', item_height + 'px', 'important');
} else {
item.height(item_height);
}
item.find('.v-align-bottom').height(item.find('.v-align-bottom').height() + add_height_for_item);
item.find('.ty-grid-list__price').height(item.find('.ty-grid-list__price').height() + add_height_for_item);
}
});
}
}
if (grid_wrapper.is(':visible')) {
calculate_list_size();
} else {
const check_wrapper_visibility = setInterval(function () {
if (grid_wrapper.find('.ab-stickers-container__price_before, .ab-stickers-container__price_after').first().is(':visible')) {
calculate_list_size();
clearInterval(check_wrapper_visibility);
}
}, 100);
}
}
});
});
})(Tygh, Tygh.$);