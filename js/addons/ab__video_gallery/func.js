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
const initObserver = new IntersectionObserver(function (entries, observer) {
entries.forEach(entry => {
if (entry.isIntersecting) {
init_video($(entry.target));
observer.unobserve(entry.target);
}
});
}, {threshold: 0.2});
const mainObserver = new IntersectionObserver(function (entries) {
entries.forEach(entry => {
let state = entry.isIntersecting && entry.target.dataset.autoplay ? 'play' : 'pause';
toggle_video(entry.target.dataset.videoId, state);
});
}, {threshold: 0.01});
const videos_queue = {Y: [], V: []};
$.ceEvent('on', 'ce.dialogshow', function (d) {
if (/ab__vg_video_/.test(d.attr('id'))) {
$('.ab__vg_loading', $(d)).trigger('click');
}
const video = $('iframe,video', d);
if (video.length) {
video.each(function () {
toggle_video((this.getAttribute('id') || this.getAttribute('data-id') || $(this).parent().get(0).getAttribute('id')), 'play');
});
}
});
$.ceEvent('on', 'ce.dialogclose', function (d) {
const video = $('iframe,video', d);
if (video.length) {
video.each(function () {
toggle_video((this.getAttribute('id') || this.getAttribute('data-id') || $(this).parent().get(0).getAttribute('id')), 'pause');
});
}
});

$.ceEvent('on', 'ce.product_image_gallery.ready', function () {
const elem = $('.cm-thumbnails-mini.active');
if (elem.length) {
const c_id = elem.data('caGalleryLargeId');
$('#' + c_id).closest('.cm-preview-wrapper').trigger('owl.jumpTo', elem.data('caImageOrder') || 0);
}
});
$.ceEvent('on', 'ce.commoninit', function (context) {
context.find('.ab__vg-image_gallery_video-autoplay:not(.ab__vg-inited)').each(function () {
initObserver.observe(this);
});
});
$(_.doc).on('click', '.ab__vg_loading', function () {
init_video($(this));
});
$(_.doc).on('click', '.ab__vg-image_gallery_video.cm-dialog-opener', function () {
const id = $(this).data('caTargetId');
if (id !== undefined) {
$('#' + id + ' .ab__vg_loading').trigger('click');
}
});
$(_.doc).on('mouseenter mouseleave', '.cm-ab-hover-gallery .item', function (e) {
const item = $(this),
wrapper = item.parents('.ut2-gl__image').first().find('.ab__vg-product_list-wrapper');
if (wrapper.length) {
const video_container = wrapper.find('.ab__vg-product_list-video'),
image_container = wrapper.find('.ab__vg-product_list-image'),
has_hover_image = video_container.hasClass('hover_image');
if (e.type === 'mouseenter' && item.data('caProductAdditionalImageSrc')) {
image_container.show();
video_container.hide();
has_hover_image && video_container.removeClass('hover');
} else {
image_container.hide();
video_container.show();
e.type === 'mouseleave' && has_hover_image && video_container.removeClass('hover ab__vg_loading');
e.type === 'mouseenter' && has_hover_image && video_container.addClass('hover');
}
}
});
$(_.doc).on('mouseenter mouseleave click', '.ab__vg-product_list-video.hover_image', function (e) {
const video_container = $(this),
is_touchable_mobile = is_touch_enabled() && is_mobile_device();
if (e.type === 'mouseenter') {
video_container.closest('.ut2-gl__image').addClass('video-play');
}
if (e.type === 'click' || e.type === 'mouseenter') {
if (is_touchable_mobile && e.type === 'mouseenter') {
e.preventDefault();
return;
}
const wrapper = video_container.find('.ab__vg-image_gallery_video-wrapper');
if (!wrapper.data('aspect')) {
const height = video_container.height() + 'px',
width = video_container.width() + 'px';
wrapper.css({width, height});
}
is_touchable_mobile && !video_container.hasClass('hover') && e.preventDefault();
video_container.addClass('hover');
} else if (e.type === 'mouseleave') {
video_container.removeClass('hover ab__vg_loading');
video_container.closest('.ut2-gl__image').removeClass('video-play');
}
});
function init_video(elem) {
let [video_id, video_type, video_path, params] = prepare_video(elem);
if (elem.hasClass('ab__vg-progress')) {
return;
} else {
elem.addClass('ab__vg-progress');
}
elem.closest('.ab__vg-product_list-video.hover_image.hover').addClass('ab__vg_loading');
if (video_type === 'Y') {
if (window['YTConfig']) {
_.ab__video_gallery.youtube_api_loaded = 1;
}
const api_state = _.ab__video_gallery.youtube_api_loaded;
if (api_state === 0) {
_.ab__video_gallery.youtube_api_loaded = -1;
window.onYouTubeIframeAPIReady = function () {
_.ab__video_gallery.youtube_api_loaded = 1;
videos_queue.Y.forEach(function (data) {
setTimeout(() => {
add_youtube_listeners.apply(this, data);
}, 20);
});
};
$.getScript('https://www.youtube.com/iframe_api');
}
if ([0, -1].includes(api_state)) {
videos_queue.Y.push([elem, video_id, video_path, video_type, params]);
} else {
add_youtube_listeners(elem, video_id, video_path, video_type, params);
}
} else if (video_type === 'V') {
const api_state = _.ab__video_gallery.vimeo_api_loaded;
if (api_state === 0) {
_.ab__video_gallery.vimeo_api_loaded = -1;
$.getScript('https://player.vimeo.com/api/player.js', function () {
_.ab__video_gallery.vimeo_api_loaded = 1;
videos_queue.V.forEach(function (data) {
setTimeout(() => {
add_vimeo_listeners.apply(this, data);
}, 20);
});
});
}
if ([0, -1].includes(api_state)) {
videos_queue.V.push([elem, video_id, video_path, video_type, params]);
} else {
add_vimeo_listeners(elem, video_id, video_path, video_type, params);
}
} else if (video_type === 'R') {
add_resource_player_listeners(elem, video_id, video_path, video_type, params);
} else {
const video = add_custom_player_listeners(elem, video_id, video_type, params);
$.ceEvent('trigger', 'ab__vg.load_custom_player', [video_type, video, video_id, params]);
}
}
function init_video_after(elem, player, video_id, video_path, video_type, params) {
elem.removeClass('ab__vg-progress').addClass('ab__vg-inited');
$.ceEvent('trigger', 'ab__vg.video_inited', [elem, player, video_id, video_path, video_type, params]);
}
function prepare_video(elem) {
const params = elem.data('abVgVideoParams') ?? {};
const video_id = elem.attr('id') || elem.attr('data-id'),
video_type = params.type ?? null,
video_path = params.path ?? null;
elem.removeClass('ab__vg_loading');
return [video_id, video_type, video_path, params];
}
function toggle_video(video_id, event) {
const player = _.ab__video_gallery.players[video_id];
if (player !== void (0)) {
if (player.type === 'Y' && typeof player.player.pauseVideo === 'function') {
if (event === 'play') {
player.player.playVideo();
} else {
player.player.pauseVideo();
}
} else if (player.type === 'V' && typeof player.player.pause === 'function') {
if (event === 'play') {
player.player.play();
} else {
player.player.pause();
}
} else if (player.type === 'R' && typeof player.player.pause === 'function') {
if (event === 'play') {
player.player.play();
} else {
player.player.pause();
}
} else {
$.ceEvent('trigger', 'ab__vg.toggle_custom_player', [video_id, player, event]);
}
}
}
function ab__vg_on_state_change(event) {
$.ceEvent('trigger', 'ab__vg.on_state_change', [this, ...arguments]);
}
function add_youtube_listeners(video_elem, video_id, video_path, video_type, params) {
const autoplay = +(params.autoplay ?? 0),
controls = +(_.ab__video_gallery.settings.controls === 'Y'),
loop = +(_.ab__video_gallery.settings.loop === 'Y');
const player = _.ab__video_gallery.players[video_id] = {
type: video_type,
autoplay: autoplay,
params: params,
player: new YT.Player(video_id, {
videoId: video_path,
width: params.width,
height: params.height,
playerVars: {
autoplay: autoplay,
controls: +(controls && !autoplay),
disablekb: +(!controls || autoplay),
enablejsapi: 1,
fs: +(controls && !autoplay),
hl: _.cart_language,
loop: +(loop || autoplay),
modestbranding: +(!autoplay),
mute: autoplay,
origin: window.location.href,
rel: 0,
showinfo: +(!autoplay),
playlist: video_path,
},
events: {
onStateChange: ({target, data}) => {
ab__vg_on_state_change.call({
video_id: video_id,
video_type: video_type,
event: data === 1 ? 'play' : 'pause'
}, {'target': target, 'data': data}, data);
if (!autoplay && data === 1) {
Object.values(_.ab__video_gallery.players).forEach(player => {
if (player.player === target) {
return;
}
!player.autoplay && player.player.pauseVideo?.();
})
} else if (data === 1) {
const {width, height} = target.playerInfo.videoContentRect;
resize_video_to_grid($(target.g), params, width, height)
}
},
onReady: ({target}) => {
if (!autoplay) {
toggle_video(video_id, 'play');
}
if (player?.player?.g) {
add_video_observer(player.player.g, video_id, player);
}
init_video_after(video_elem, player, video_id, video_path, video_type, params);
},
onError({target}) {
const {errorCode} = target.getVideoData();
errorCode === "auth" && target.stopVideo().playVideo();
}
}
})
};
}
function add_vimeo_listeners(video_elem, video_id, video_path, video_type, params) {
const autoplay = +(params.autoplay ?? 0),
controls = +(_.ab__video_gallery.settings.controls === 'Y') || true,
loop = +(_.ab__video_gallery.settings.loop === 'Y');
const player = _.ab__video_gallery.players[video_id] = {
type: video_type,
autoplay: autoplay,
params: params,
player: new Vimeo.Player($('#' + video_id).get(0), {
id: video_path,
autoplay: autoplay,
background: autoplay,
byline: +(!autoplay),
cc: +(!autoplay),
chromecast: +(controls && !autoplay),
controls: +(controls && !autoplay),
fullscreen: +(controls && !autoplay),
interactive_markers: +(!autoplay),
keyboard: 0,
loop: +(loop || autoplay),
muted: autoplay,
pip: 0,
progress_bar: +(controls && !autoplay),
quality_selector: +(controls && !autoplay),
title: +(!autoplay),
transcript: +(controls && !autoplay),
vimeo_logo: +(!autoplay),
volume: +(controls && !autoplay),
watch_full_video: +(controls && !autoplay)
})
};
video_elem.find('img').remove();
"play pause".split(" ").forEach(function (e) {
player.player.on(e, (event) => {
ab__vg_on_state_change.call({
video_id: video_id,
video_type: video_type,
event: e
}, player.player, event);
});
});
if (autoplay) {
Promise.all([player.player.getVideoWidth(), player.player.getVideoHeight()])
.then(([width, height]) => {
resize_video_to_grid(video_elem, params, width, height)
})
}
player.player.getVideoTitle().then(function () {
$.each(video_elem.data(), function (i, val) {
if (i !== 'src') {
video_elem.attr(i, val);
}
});
if (!player.autoplay) {
toggle_video(video_id, 'play');
}
add_video_observer(video_elem.get(0), video_id, player);
init_video_after(video_elem, player, video_id, video_path, video_type, params);
});
}
function add_resource_player_listeners(video_elem, video_id, video_path, video_type, params) {
let video = $('<video></video>');
if (video_elem.prop('tagName') === 'VIDEO') {
video = video_elem;
} else {
video.attr('id', video_id);
video.addClass(video_elem.attr('class'));
video.attr('controls', true);
video.attr('playsinline', true);
if (params.autoplay) {
video.attr('autoplay', true);
video.attr('muted', true);
}
if ((_.ab__video_gallery.settings.controls !== 'Y' && false ) || params.autoplay) {
video.attr('controls', false);
}
if ((_.ab__video_gallery.settings.loop === 'Y') || params.autoplay) {
video.attr('loop', true);
}
$.each(video_elem.data(), function (i, val) {
if (i !== 'abVgVideoParams') {
video.attr(i, val);
}
});
video.removeClass('ab__vg_video_player');
const video_wrapper = $('<div class="ab__vg_video_player"></div>');
video_wrapper.append(video);
video_elem.replaceWith(video_wrapper);
}
const player = _.ab__video_gallery.players[video_id] = {
player: video.get(0),
type: video_type,
autoplay: params.autoplay,
params: params
};
['play', 'pause'].forEach(function (e) {
player.player.addEventListener(e, (event) => {
ab__vg_on_state_change.call({
video_id: video_id,
video_type: video_type,
event: e
}, player.player, event);
if (e === 'play') {
window.requestAnimationFrame(() => {
$(event.target).closest('.ab__vg-product_list-video').removeClass('ab__vg_loading');
});
}
});
});
let ready_callback_triggered = false;
const ready_callback = function () {
if (ready_callback_triggered) {
return;
}
ready_callback_triggered = true;
if (!player.autoplay) {
toggle_video(video_id, 'play')
}
add_video_observer(video_elem.get(0), video_id, player);
init_video_after(video_elem, player, video_id, video_path, video_type, params);
}
player.player.addEventListener('loadeddata', function () {
ready_callback();
});
if (player.player.readyState > 3) {
ready_callback();
}
}
function add_custom_player_listeners(video_elem, video_id, video_type, params) {
const video_path = video_elem.data('src'),
video = $('<iframe></iframe>');
video.attr('id', video_id);
video.addClass(video_elem.attr('class'));
$.each(video_elem.data(), function (i, val) {
if (i !== 'abVgVideoParams') {
video.attr(i, val);
}
});
const player = _.ab__video_gallery.players[video_id] = {
player: video.get(0),
type: video_type,
autoplay: params.autoplay,
params: params
};
video.on('load', function () {
if (!player.autoplay) {
toggle_video(video_id, 'play');
}
add_video_observer(video.get(0), video_id, player);
init_video_after(video, player, video_id, video_path, video_type, params);
});
video_elem.replaceWith(video);
return video;
}
function add_video_observer(video_element, video_id, player) {
video_element.dataset.autoplay = player.autoplay;
video_element.dataset.videoId = video_id;
mainObserver.observe(video_element);
}
function is_mobile_device() {
let check = false;
(function (a) {
if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) {
check = true;
}
})(navigator.userAgent || navigator.vendor || window.opera);
return check;
};
function is_touch_enabled() {
return 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;
}
function resize_video_to_grid($el, params, width, height) {
const parent = $el.closest('.ab__vg-image_gallery_video-wrapper');
if (parent.data('aspect') || !parent.parent('.ab__vg-product_list-video').length) {
return;
}
const aspect = width / height,
parentAspect = params.width / params.height,
wrapperWidth = parent.parent().outerWidth(),
wrapperHeight = parent.parent().outerHeight();
parent.data('aspect', aspect);
parent.css({'--vaspect': aspect.toFixed(3), '--taspect': parentAspect.toFixed(3)})
let value;
if (aspect > parentAspect) {
value = {width: '', height: wrapperHeight + 'px', justifyItems: 'center'};
} else {
value = {height: '', width: wrapperWidth + 'px', alignItems: 'center'};
}
parent.css(value);
parent.parent('.ab__vg-product_list-video').removeClass('ab__vg_loading');
}
})(Tygh, Tygh.$);