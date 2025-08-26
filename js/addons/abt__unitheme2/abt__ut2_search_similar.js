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
const HASH_SEPARATOR = '_',
HASH_FEATURE_SEPARATOR = '-';
let builFeaturesHash = (container) => {
var features = {};
var hash = [];
container.find('input.cm-ab-similar-filter:checked').each(function () {
var elm = $(this);
if (!features[elm.data('caFilterId')]) {
features[elm.data('caFilterId')] = [];
}
features[elm.data('caFilterId')].push(elm.val());
});
for (var k in features) {
hash.push(k + HASH_FEATURE_SEPARATOR + features[k].join(HASH_FEATURE_SEPARATOR));
}
return hash.join(HASH_SEPARATOR);
}
let loadCategory = (container) => {
let baseUrl = container.data('caBaseUrl'),
targetUrl = $.attachToUrl(baseUrl, 'features_hash=' + builFeaturesHash(container));
$.redirect(targetUrl);
};
let toggleButtonState = (container, element) => {
if (element.data('ab-show-search-button')) {
let parentElement = element.parent('.ty-product-feature__multiple-item');
if (!parentElement.length) {
parentElement = element.parent('.ty-product-feature__value');
}
if (parentElement.length > 0 && !parentElement.find('.abt__ut2_search_similar_in_category_btn ').length) {
parentElement.append($(_.abt__ut2.templates.search_similar_button));
}
}
let checkboxesState = !!container.find('.cm-ab-similar-filter:checked').length;
container.find('.abt__ut2_search_similar_in_category_btn').toggleClass('disabled',!checkboxesState);
};
$.ceEvent('on', 'ce.commoninit', function(context) {
const containers = context.find('.cm-ab-similar-filter-container');
if (containers.length) {
containers.each(function(i, container) {
container = $(container);
container.on('change', '.cm-ab-similar-filter', function (e) {
toggleButtonState($(e.delegateTarget), $(e.target));
});
container.on('click', '.abt__ut2_search_similar_in_category_btn:not(.disabled)', function (e) {
loadCategory($(e.delegateTarget));
});
container.find('.cm-ab-similar-filter').first().trigger('change');
});
}
});
}(Tygh, Tygh.$));