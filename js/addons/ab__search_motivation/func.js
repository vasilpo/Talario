/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2020   *
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
$.ceEvent('on', 'ce.commoninit', function(context) {
var is_ltr = _.language_direction === "ltr";
setTimeout(function(){
var input = context.find('#search_input:not([data-cur-placeholder-string])');
if (input.length && _.ab__sm.phrases.length && typeof _.ab__sm.phrases === "object") {
_.ab__sm.theater = new TheaterJS();
_.ab__sm.theater.describe("SearchBox", .8, "#search_input");
input.removeClass('cm-hint').val('');
var v_input = input[0];
var title = v_input.getAttribute("title");
v_input.setAttribute("data-cur-placeholder-string", title);
v_input.setAttribute("name", 'q');
var erase = function(title, tmp_c) {
v_input.setAttribute("placeholder", (!is_ltr ? '|' : '') + title.substring(0, title.length - tmp_c) + (is_ltr ? '|' : ''));
if (title.length - tmp_c) {
setTimeout(function(){ erase(title, tmp_c + 1); }, 75);
} else {
_.ab__sm.phrases.forEach(function(item) {
if (item !== '') {
_.ab__sm.theater.write("SearchBox:" + item.trim()).write({
name: 'wait',
args: [2000]
});
}
});
_.ab__sm.theater.write(function () {
_.ab__sm.theater.play(true);
});
}
};
erase(title, 0);
}
}, _.ab__sm.delay);
});
})(Tygh, Tygh.$);