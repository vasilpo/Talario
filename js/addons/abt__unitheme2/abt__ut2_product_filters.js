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
function toggleColorFilter(button) {
var $btn = $(button);
var displayLimit = parseInt($btn.data('display-count'), 10) || 0;
var $filterBlock = $btn.prev('.ty-product-filters__color-filter');
var $items = $filterBlock.find('li');
$filterBlock.toggleClass('none-overflow');
$btn.toggleClass('open');
if ($btn.hasClass('open')) {
$items.removeClass('hidden').addClass('ty-product-filters__color-list-item');
} else {
$items.each(function (index) {
if (index >= displayLimit) {
$(this).addClass('hidden').removeClass('ty-product-filters__color-list-item');
}
});
}
}
$.ceEvent('on', 'ce.commoninit', function (context) {
$('[data-toggle-color-filter]', context).off('click.toggleColorFilter').on('click.toggleColorFilter', function () {
toggleColorFilter(this);
});
});
})(Tygh, Tygh.$);
