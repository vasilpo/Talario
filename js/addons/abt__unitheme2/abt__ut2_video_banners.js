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
let observer;
function playVideo(el) {
if (el.tagName === 'VIDEO') {
el.paused && el.play();
} else {
el.contentWindow?.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
}
}
function pauseVideo(el) {
if (el.tagName === 'VIDEO') {
!el.paused && el.currentTime > 0 && el.pause();
} else {
el.contentWindow?.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
}
}
document.addEventListener('DOMContentLoaded', function () {
observer = new IntersectionObserver((entries) => {
entries.forEach((entry) => {
let videoElem = entry.target;
entry.isIntersecting ? playVideo(videoElem) : pauseVideo(videoElem);
})
}, {});
})
$.ceEvent('on', 'ce.commoninit', function (context) {
$('video.ut2-banner__video, .ut2-a__video > iframe', context).each((k, i) => {
observer.observe(i);
});
$('.internal-video .ut2-a__video[data-is-autoplay="Y"]', context).each((k, i) => {
_.abt__ut2.functions.fn_abt__ut2_load_video($(i));
});
});
$.ceEvent('on', 'abt__ut2.youtube_iframe_loaded', function (el, params) {
observer.observe(el);
});
}(Tygh, Tygh.$));