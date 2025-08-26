<script>
(function(_, $) {
$(_.doc).on('click', '.compatible-title', function (e) {
var ct = $(this).parent().find('div.compatible-text');
if (ct.hasClass('hidden')) ct.removeClass('hidden');
else ct.addClass('hidden');
});
$.ceEvent('on', 'ce.commoninit', function(context) {
var tooltips = {if $abam_tooltips}{$abam_tooltips|json_encode nofilter}{else} { } {/if};
if (Object.keys(tooltips).length !== 0){
$.each(tooltips, function(addon, addon_items) {
$.each(addon_items, function(item, value) {
context.find(value.selector).append('<a target="_blank" href="' + value.url + '" class="clearfix ab-am-tooltip"><i class="icon-share"></i>{__("ab__am.note")}</a>');
});
});
}
});
$.ceEvent('on', 'ce.commoninit', function(context) {
var ab_am_events = {if $abam_events}{$abam_events|json_encode nofilter}{else}{ available_updates: { } }{/if};
var available_updates = Object.keys(ab_am_events.available_updates).length;
if (available_updates){
var menu = $('.navbar-admin-top .nav-pills');
if (menu.find('.ab__addons_manager').length){
menu.find('.ab__addons_manager').parent().parent().find('a:first > b').before('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates"></span>');
menu.find('.ab__addons_manager').find('a.ab__am').append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates">' + available_updates + '</span>');
}
var menu = $('.adv-buttons');
if (menu.find('.ab__am-menu').length){
menu.find('.ab__am-menu').children('a').append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates"></span>');
if (menu.find('.ab__am-menu').find('.ab__am').length){
menu.find('.ab__am-menu').find('.ab__am').append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates">' + available_updates + '</span>');
}
}
var li_a = $('#elm_developer_pages a:contains("AlexBranding")');
if (li_a.length){
if (li_a.find('.ab-am-available-updates').length){
li_a.find('.ab-am-available-updates').remove();
}
li_a.append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates">' + available_updates + '</span>');
}
}
});
function delay(callback, ms) {
var timer = 0;
return function() {
var context = this, args = arguments;
clearTimeout(timer);
timer = setTimeout(function () {
callback.apply(context, args);
}, ms || 0);
};
}
$('#ab__am_search').on('keyup input', delay(function (e) {
var str = $(this).val();
if (str.length) {
$('#ab__am_search__clear').removeClass('hidden');
} else {
$('#ab__am_search__clear').addClass('hidden');
}
$('.ab__am-section.collapsed').click();
$('.ab-am-set-name-wrapper:not(.open)').click();
$('table.ab-am-addons tbody').each(function (index, element) {
if ($(this).find('.ab-am-addon-name,.ab-am-addon-description').text().toUpperCase().indexOf(str.toUpperCase()) >= 0){
$(this).removeClass('hidden');
} else {
$(this).addClass('hidden');
}
});
$('#ab__am_available_sets > table.ab-am-table > tbody').each(function () {
var all = 0;
var hidden = 0;
all = $(this).find('.ab-am-addons > tbody').size();
hidden = $(this).find('.ab-am-addons > tbody.hidden').size();
if (all == hidden){
$(this).addClass('hidden');
}else{
$(this).removeClass('hidden');
}
});
$('#ab__am_search__clear').on('click', function(){
$('#ab__am_search').val('').focus();
$('table.ab-am-addons tbody').removeClass('hidden');
$('table.ab-am-set tbody').removeClass('hidden');
$(this).addClass('hidden');
});
}, 500));
}(Tygh, Tygh.$));
</script>
