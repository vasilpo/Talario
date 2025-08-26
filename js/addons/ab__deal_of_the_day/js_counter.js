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
function ab_dotd_js_counter(id, total_seconds, langs) {
var elem = document.getElementById(id);
if (total_seconds <= 0 || elem.dataset.ab__dotd_inited !== undefined) {
return true;
}
elem.dataset.ab__dotd_inited = true;
elem.style.visibility = 'hidden';
var dom_elems = {};
dom_elems['days'] = document.createElement('div');
dom_elems['days'].className = 'ab-dotd-js-counter_days';
elem.appendChild(dom_elems['days']);
dom_elems['hours'] = document.createElement('div');
dom_elems['hours'].className = 'ab-dotd-js-counter_hours';
elem.appendChild(dom_elems['hours']);
dom_elems['minutes'] = document.createElement('div');
dom_elems['minutes'].className = 'ab-dotd-js-counter_minutes';
elem.appendChild(dom_elems['minutes']);
dom_elems['seconds'] = document.createElement('div');
dom_elems['seconds'].className = 'ab-dotd-js-counter_seconds';
elem.appendChild(dom_elems['seconds']);
var interval = setInterval(function() {
total_seconds--;
var counts = {
days: Math.floor(total_seconds / (60 * 60 * 24)),
hours: Math.floor((total_seconds % (60 * 60 * 24)) / (60 * 60)),
minutes: Math.floor((total_seconds % (60 * 60)) / (60)),
seconds: Math.floor((total_seconds % (60))),
};
if (counts['days'] === 0) {
dom_elems['days'].style.display = 'none';
}
['days', 'hours', 'minutes', 'seconds'].forEach((item) => {
var evalutaion = eval(Tygh.ab__dotd.plural_formula.replaceAll('$number', 'counts["' + item + '"]'));
if (langs[item][evalutaion] !== null && langs[item][evalutaion] !== undefined ) {
var template = langs[item][evalutaion].replace('[n]', '<span>' + counts[item] + '</span>');
if (dom_elems[item].innerHTML !== template) {
dom_elems[item].innerHTML = template;
}
}
});
if (total_seconds < 0) {
clearInterval(interval);
}
elem.style.visibility = 'visible';
}, 1000);
}