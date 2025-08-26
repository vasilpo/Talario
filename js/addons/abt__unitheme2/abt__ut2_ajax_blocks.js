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
$(document).ready(function() {
const margin = _.abt__ut2?.settings.general.block_loader_additional_margin === undefined ? 100 : parseInt(_.abt__ut2.settings.general.block_loader_additional_margin);
const observer = new IntersectionObserver(observerCallback, {rootMargin: `${margin}px 0px ${margin}px 0px`});
function observerCallback(entries, observer) {
entries.forEach((entry) => {
if (entry.isIntersecting) {
$('[class^=cm-block-loader--]', entry.target).ceBlockLoader();
observer.unobserve(entry.target);
}
});
}
$.ceEvent('on', 'ce.commoninit', function (context) {
if (context.is('.cm-block-loader')) {
if (context.find('.cm-block-loaded').length) {
context.closest('.cm-ut2-block-loader').addClass('ut2-block-loaded').removeClass('ut2-block-loading');
} else {
context.closest('.cm-ut2-block-loader').remove();
}
}
$('.cm-ut2-block-loader:not(.ut2-block-loaded)', context).each(function () {
observer.observe(this);
});
});
});
}(Tygh, Tygh.$));
