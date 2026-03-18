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
window.ut2_load_products = function(button){
button.classList.add('loading');
let grid_list = button.parentNode.parentNode;
let data = {
block_id: button.attributes['data-ca-block-id'].value,
current_page: button.attributes['data-ca-current-page'].value,
grid_id: button.attributes['data-ca-grid-id'].value,
snapping_id: button.attributes['data-ca-snapping'].value,
request_params: button.attributes['data-ca-request-params'].value,
};
document.getElementById(button.id).remove();
$(grid_list).append('<span class="ab-grid-loader"></span>');
$.ceAjax('request', fn_url('abt__ut2_load_blocks.get_block_content'), {
hidden: true,
data: data,
method: 'get',
pre_processing: function () {
grid_list.getElementsByClassName('ab-grid-loader')[0].remove();
},
callback: function (response) {
$.ceEvent('trigger', 'abt__ut2.load_products_block_content', [response]);
if (button.attributes['data-ca-load-type'].value === 'onclick_and_scroll') {
let gls = grid_list.getElementsByClassName('grid-list__load-more');
for (const gl of gls) {
gl.classList.add('view');
}
window.onscroll = function() {
let new_button = document.getElementById(button.id);
if (new_button !== undefined && new_button !== null && !new_button.classList.contains('loading')) {
let window_height = window.innerHeight,
scroll_top = window.pageYOffset,
additional_margin = 100;
let button_top = scroll_top + new_button.getBoundingClientRect().top;
let gls = grid_list.getElementsByClassName('grid-list__load-more');
for (const gl of gls) {
gl.classList.add('view');
}
if (scroll_top + window_height + additional_margin > button_top && scroll_top - window_height - additional_margin < button_top) {
new_button.onclick();
}
}
}
} else if (button.attributes['data-ca-load-type'].value === 'onclick') {
let gls = grid_list.getElementsByClassName('grid-list__load-more');
for (const gl of gls) {
gl.classList.add('view');
}
}
},
});
};
$.ceEvent('on', 'abt__ut2.load_products_block_content', function(response){
let elem = $(document.getElementById(response.key));
const isWrapped = elem.children().length === 1 && elem.children('[id^="snapping_id_"]').length === 1;
if (elem.find('.cm-warehouse-block-depends-by-location').length || isWrapped){
$($(elem.contents().unwrap()).contents().unwrap()).contents().unwrap();
} else {
$(elem.contents().unwrap()).contents().unwrap();
}
});
}(Tygh, Tygh.$));