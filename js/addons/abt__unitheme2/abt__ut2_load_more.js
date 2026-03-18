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
let use_temp_context = true;
const temp_contexts = [];
$(document).ready(function(){
const mode = _.abt__ut2.settings.load_more.mode[_.abt__ut2.device];
_.abt__ut2.temporary.load_more_button_clicked = mode !== 'semi_auto';
$(_.doc).on('click', '.ut2-load-more:not(.hidden):not(.ut2-load-more-loading)', function() {
_.abt__ut2.temporary.load_more_button_clicked = true;
$(this).addClass('ut2-load-more-loading');
let current_position = 0;
$.ceAjax('request', $(this).data('ut2-load-more-url'), {
save_history: true,
result_ids: $(this).data('ut2-load-more-result-ids'),
append: true,
hidden: true,
pre_processing: function (data, params){

var grid_items = $('.cm-pagination-container').find('div[class*="ty-column"]');
if (grid_items.length) {
var empty_items = $('.' + 'ty-column' + grid_items[0].className.match(/ty-column(\d)+/)[1] + ':empty');
if (empty_items.length) {
empty_items.remove();
}
}
current_position = $(window).scrollTop();
$('html').addClass('dialog-is-open');
},
callback: function(data) {
$(window).scrollTop(current_position);
$('html').removeClass('dialog-is-open');
$('.ut2-load-more-loading').addClass('hidden');
if (data.html?.ut2_pagination_block_bottom !== undefined) {
$('#ut2_pagination_block_bottom').empty().html(data.html.ut2_pagination_block_bottom);
}
if (data.html?.ut2_pagination_block !== undefined){
$('#ut2_pagination_block').empty().html(data.html.ut2_pagination_block);
}
$.ceEvent('trigger', 'ce.ut2-load-more', [data]);
},
});
});
if (['auto', 'semi_auto'].includes(mode)) {
const additionalMargin = 250;
const observer = new IntersectionObserver((entries, observer) => {
entries.forEach(entry => {
if (entry.isIntersecting && !document.querySelector('html').classList.contains('dialog-is-open') && !_.scrolling) {
if (_.abt__ut2.temporary.load_more_button_clicked) {
document.querySelector('.ut2-load-more:not(.hidden):not(.ut2-load-more-loading)')?.click();
}
}
});
}, {
rootMargin: `${additionalMargin}px`,
});
const process_context = function (context) {
let paginationBottom = context.find('.ty-pagination__bottom');
if (paginationBottom.length) {
observer.disconnect();
observer.observe(paginationBottom[0]);
}
if (mode === 'semi_auto') {
let remove_class = 'ut2-load-more-semi_auto';
let add_class = 'ut2-load-more-auto';
let target;
if (context.hasClass('ut2-load-more-container')) {
target = context;
} else {
target = context.find('.ut2-load-more-container');
}
if (!_.abt__ut2.temporary.load_more_button_clicked) {
remove_class = 'ut2-load-more-auto';
add_class = 'ut2-load-more-semi_auto';
}
target.removeClass(remove_class).addClass(add_class);
}
}
temp_contexts.forEach(function (context) {
process_context(context);
});
$.ceEvent('on', 'ce.commoninit', function(context) {
use_temp_context = false;
process_context(context);
});
}
});
$.ceEvent('on', 'ce.commoninit', function(context) {
if (use_temp_context) {
temp_contexts.push(context);
}
});
}(Tygh, Tygh.$));
