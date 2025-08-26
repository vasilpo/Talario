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
context.find('.ty-product-bigpicture__img').prepend(context.find('.ty-product-bigpicture__img > .ab-stickers-wrapper'));
});
$.ceEvent('on', 'ab__stickers.before_process', function (context, items) {
context.find('.ab-stickers-container__price_before').each(function (i, container) {
container = $(container);
if (container.parents('.ty-simple-list__price').length) {
container.parents('.ty-simple-list__price').first().before(container);
}
if (container.parents('.ty-product-list__price').length) {
container.parents('.ty-product-list__price').first().before(container);
}
});
});
})(Tygh, Tygh.$);