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
$(_.doc).ready(function() {
add_title_to_descr($('.ab-mb-add-title-to-descr option:selected'));
});
$('.ab-mb-templates-select select').change(function(e) {
var self = $(this);
var selected = self.find('option:selected');
var settings = selected.data('settings');
var all_tmpl_settings_wrappers = $(_.doc).find("a[id^='sw_mb_ts_'], div[id^='mb_ts_']");
all_tmpl_settings_wrappers.hide();
all_tmpl_settings_wrappers.find('.cm-required').attr('data-required', '').removeClass('cm-required');
if (settings) {
var selected_tmlp_settings = $(_.doc).find(`#sw_mb_ts_${settings}, #mb_ts_${settings}`);
selected_tmlp_settings.css('display', '');
selected_tmlp_settings.find('[data-required]').removeAttr('data-required').addClass('cm-required');
}
add_title_to_descr(selected);
});
function add_title_to_descr(option) {
var title = option.attr('title');
var descr = option.parents('.control-group').find('.description');
if (title != void(0) && title.toString().length) {
descr.show();
descr.text(title);
} else {
descr.hide();
}
}
})(Tygh, Tygh.$);