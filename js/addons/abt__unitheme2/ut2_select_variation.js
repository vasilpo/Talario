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
context.find('.cm-ab-load-select-variation-content').click(function(){
var btn = this;
var product_id = btn.getAttribute('data-ca-product-id');
const combination = btn.getAttribute('data-ca-combination');
var target_id = 'ut2_select_variation_wrapper_' + product_id;
var existing_target = document.getElementById(target_id);
if (existing_target !== null) {
existing_target.remove();
}
var target = $(`<div id="${target_id}" class="ab-mobile-select-variation-container select-variation-dialog hidden"></div>`);
target.appendTo('#' + _.container);
const data = {
product_id: product_id,
get_view_content: target_id,
}
if(combination){
data.combination = combination;
}
$.ceAjax('request', fn_url('products.ut2_select_variation'), {
method: 'get',
hidden: false,
data: data,
callback: function (res) {
if (res.html[target_id] !== undefined && res.html[target_id] !== null) {
target.html(res[target_id]);
btn.classList.add('loaded');
window.g_fn__showDialog(target, 'type-_select-variation');
$.ceEvent('trigger', 'ce.commoninit', [target]);

$.ceEvent('trigger', 'ut2_load_select_variation', [target, btn]);
}
}
});
});
});
}(Tygh, Tygh.$));
