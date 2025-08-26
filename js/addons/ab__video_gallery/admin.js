/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2024   *
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
$(_.doc).ready(function () {
$('#content_ab__video_gallery .cm-row-item').each(function () {
process_video_row($(this), true);
});
});
_.ab__vg.change_video_props_trigger = function (type, input, value) {
let inputs_wrapper = $(input).parents('.cm-row-item');
if (!inputs_wrapper.length) {
inputs_wrapper = $('#box_add_ab__vg_video');
}
if (type === 'video_type') {
const icon_types_inputs = inputs_wrapper.find('.ab__vg__icon_type input');
if (['H', 'R'].includes(value)) {
icon_types_inputs.attr('disabled', '');
inputs_wrapper.find('.ab__vg__icon_type [value="icon"]').prop('checked', true).removeAttr('disabled');
} else {
icon_types_inputs.removeAttr('disabled');
}
}
if (type === 'product_pos_type') {
if (value === 'C') {
inputs_wrapper.find('.ab__vg-product_pos').removeClass('hidden');
} else {
inputs_wrapper.find('.ab__vg-product_pos').addClass('hidden');
}
}
inputs_wrapper.find('.cm-ab-video-settings-wrapper').addClass('hidden');
inputs_wrapper.find('.cm-ab-video-settings-wrapper[data-wrapper-for="' + value + '"]').removeClass('hidden');
}
const process_video_icon_type = function(container, trigger) {
const icon_types = container.find('.ab__vg__icon_type input[type="radio"]'),
autoplay = container.find('[id^="ab__vg__autoplay"]').is(':checked'),
icon_none = icon_types.filter('[value="none"]');
icon_none.prop('disabled', !autoplay);
if (trigger?.checked === false && icon_none.is(':checked')) {
icon_types.get(0).checked = true;
}
}
const process_video_row = function (box, load = false) {
const inputs = box.find('input, textarea, select');
if (!load) {
box.find('input[id^="ab__vg__autoplay"], input[id^="ab__vg__show_in_list"]').prop("checked", false);
box.find('input[id^="ab__vg__show_in_list"]').attr('disabled', 'disabled');
}
process_video_icon_type(box);
inputs.change(function (e) {
e.target.name.endsWith('[autoplay]') && process_video_icon_type(box, e.target);
box.find('.cm-ab-vg-required').addClass('cm-required');
});
};
const observer = new MutationObserver(mutations => {
for (let mutation of mutations) {
for (let node of mutation.addedNodes) {
if (!(node instanceof HTMLElement)) continue;
const row = $(node);
row.find('.cm-required').remove();
process_video_row(row);
}
}
});
observer.observe(_.doc.querySelector('#content_ab__video_gallery table'), {
childList: true,
subtree: false,
characterDataOldValue: true
});
let autoplay_change_process = false;
$(_.doc).on('change', 'input[id^="ab__vg__autoplay"]', function () {
const input = $(this);
if (!autoplay_change_process) {
autoplay_change_process = true;
$('#content_ab__video_gallery [id^="ab__vg__autoplay"]').each(function (i, e) {
e = $(e);
if (e.attr('id') !== input.attr('id')) {
e.prop('checked', false);
}
const checked = e.is(':checked');
const show_in_list_input = e.parents('.cm-row-item').first().find('[id^="ab__vg__show_in_list"]');
if (checked) {
show_in_list_input.removeAttr('disabled');
} else {
show_in_list_input.attr('disabled', 'disabled');
}
});
autoplay_change_process = false;
}
});
})(Tygh, Tygh.$);