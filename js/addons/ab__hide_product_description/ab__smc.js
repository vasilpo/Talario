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
$.ceEvent('on', 'ce.commoninit', (context) => {
let selector = _.ab__smc.selector + (_.ab__smc.additional_selector.parent_selectors.length ? ',' + _.ab__smc.additional_selector.parent_selectors.join(',') : '');
if (selector.length) {
let elems = context.find(selector);
if (elems.length) {
$.each(elems, function () {
let elem = $(this);
let isTabsWrapHidden = false;
if (elem.is(_.ab__smc.exclude.parent_selectors.join(',')) ||
elem.find(_.ab__smc.exclude.selectors_in_content.join(',')).length) {
return;
}
let more_txt = _.tr('ab__smc.more');
let less_txt = _.tr('ab__smc.less');
let max_height = parseInt(_.ab__smc.max_height);

let tab_hide = elem.attr('data-ab-smc-tab-hide');
if (tab_hide !== void(0) && tab_hide.length) {
tab_hide = tab_hide.split('|');
let device = 2;
if (window.innerWidth < 1280 && window.innerWidth > 768) {
device = 1;
} else if (window.innerWidth < 768) {
device = 0;
}
if (tab_hide[device] === 'N') {
return;
}

more_txt = elem.attr('data-ab-smc-more') || _.tr('ab__smc.more');
less_txt = elem.attr('data-ab-smc-less') || _.tr('ab__smc.less');
if (elem.attr('data-ab-smc-tab-override-h') === 'Y') {
max_height = parseInt(elem.attr('data-ab-smc-height'));
}

isTabsWrapHidden = !elem.parent().is(':visible');
isTabsWrapHidden && elem.parent().show();
}
let elem_height = parseInt(elem.outerHeight());

isTabsWrapHidden && elem.parent().hide();
if (elem_height > max_height) {
let clickable = $("<div class='ab-smc-more" + _.ab__smc.additional_classes_for_parent + "'>" +
"<span class='" + _.ab__smc.additional_classes + "'>" + more_txt + '<i class=\'ab-smc-arrow\'></i></span>' +
'</div>').appendTo(elem);

elem.find('> *').wrapAll('<div>');

elem.addClass('ab-smc-description' + _.ab__smc.description_element_classes).css({
"max-height": max_height + 'px',
});
let timeout = _.ab__smc.transition * 0.65 * 1000; 
clickable.on('click', () => {
let blockContent = $(this);
let wrapBlock = blockContent.find('> div:first-child');
blockContent.stop(true, true).animate({height: wrapBlock.height() + clickable.height() + 20 + 'px'}, timeout, () => {
blockContent.addClass('ab-smc-opened').css({height:''});
if (!_.ab__smc.show_button) {
elem.find('.ab-smc-more').detach();
return;
}
elem.find('.ab-smc-more').html("<span class='" + _.ab__smc.additional_classes + "'>" + less_txt + "<i class='ab-smc-arrow'></i></span>").addClass('ab-smc-opened');
elem.find('.ab-smc-button').addClass('ab-smc-opened');
});
});
_.ab__smc.show_button && elem.on('click', '.ab-smc-opened', () => {
let blockContent = $(this);
blockContent.stop(true, true).animate({height: max_height + 'px'}, timeout, () => {
blockContent.removeClass('ab-smc-opened');
elem.find('.ab-smc-more').html("<span class='" + _.ab__smc.additional_classes + "'>" + more_txt + "<i class='ab-smc-arrow'></i></span>").removeClass('ab-smc-opened');
elem.find('.ab-smc-button').removeClass('ab-smc-opened');
});
});
}
});
$.ceEvent('trigger', 'ab.hide_product_description.hide', [context, elems, _.ab__smc]);
}
}
});
}(Tygh, Tygh.$));