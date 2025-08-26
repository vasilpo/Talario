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
function fn_abt__ut2_parse_youtube_id_from_url(value) {
let pattern = /^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/;
let result = pattern.exec(value);
if (result == null) {
return {status: false, value: ""};
}
return {status: true, value: result[1]};
}
function fn_abt__ut2_check_youtube_id(value) {
let pattern = /^[0-9a-zA-Z_-]{11}$/;
let result = pattern.exec(value);
if (result == null) {
return {status: false, value: ""};
}
return {status: true, value: result[0]};
}
$('#elm_banner_abt__ut2_youtube_id').on("blur", function () {
var elm = $(this);
if (elm.val().length){
let youtube_id = '';
let result_yt_id = fn_abt__ut2_check_youtube_id(elm.val());
let result_yt_url = fn_abt__ut2_parse_youtube_id_from_url(elm.val());
if (result_yt_id.status){
youtube_id = result_yt_id.value;
} else if (result_yt_url.status){
let check_youtube_id = fn_abt__ut2_check_youtube_id(result_yt_url.value);
if (check_youtube_id.status){
youtube_id = check_youtube_id.value;
}
}
let msg;
if (youtube_id.length){
msg = '<img src="https://img.youtube.com/vi/' + youtube_id + '/mqdefault.jpg" style="width: 160px;" alt="' + youtube_id + '">';
} else {
msg = '<span class="alert">' + _.tr('error') + '</span>';
}
elm.parent().find('.img').empty().append(msg);
}
});
$.ceFormValidator('registerValidator', {
class_name: 'abt__ut2_youtube_id',
message: _.tr('error'),
func: function (id) {
let val = $('#' + id).val();
if (val.length) {
let result_yt_id = fn_abt__ut2_check_youtube_id(val);
let result_yt_url = fn_abt__ut2_parse_youtube_id_from_url(val);
if (result_yt_id.status) {
return true;
} else if (result_yt_url.status) {
let check_youtube_id = fn_abt__ut2_check_youtube_id(result_yt_url.value);
if (check_youtube_id.status) {
return true;
}
return false
}
}
return true;
}
});
}(Tygh, Tygh.$));
