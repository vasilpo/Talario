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
function fn_abt__ut2_load_video (elm){
var id = elm.data('banner-youtube-id'), params = elm.data('banner-youtube-params');
if (!params.includes('autoplay=1')) {
params+='&mute=1&autoplay=1'
}
const iframe = $('<iframe>', {
type:"text/html",
src: "https://www.youtube.com/embed/"+ id +"?" + params,
frameborder: 0,
allowfullscreen: 'true',
allowscriptaccess: 'always',
}).on('load',function(){
$.ceEvent('trigger', 'abt__ut2.youtube_iframe_loaded', [this, params]);
})
elm.addClass('loaded').empty().append(iframe);
}
$(_.doc).on('click', 'a[data-content="video"]:not(.loaded),div[data-banner-youtube-id]', function(e) {
if ($(e.target).attr('data-banner-youtube-id') || $(e.target).attr('data-type')){
var elm = $(e.target);
if ($(e.target).attr('data-type')) elm = elm.parent();
$(this).addClass('loaded');
fn_abt__ut2_load_video(elm);
return false;
} else{
return true;
}
});
$(document).ready(function(){
$.extend(_.abt__ut2.functions, { fn_abt__ut2_load_video });
});
}(Tygh, Tygh.$));