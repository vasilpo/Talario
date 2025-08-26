{capture name="mainbox"}
{if $go_to_all_stores}
<p>{__("ab__am.go_to_all_stores", ["[link]" => "ab__am.addons?switch_company_id=0"|fn_url])}</p>
{elseif !empty($d)}
{$installed_addons=$d.installed_addons}
{$all_products=$d.all_products}
{$available_products=$d.available_products}
{$events=$d.events}
{$cs_addons=$addons}
{include file="addons/ab__addons_manager/views/ab__am/components/notifications.tpl"}
<script>
(function(_, $) {
$(_.doc).on('click', '.ab__am-section', function() {
var id = $(this).data('target').replace('#', '');
if ($(this).hasClass('collapsed')) {
$.cookie.set(id, 'close', new Date("January 1, 2040 00:00:00"));
} else {
$.cookie.remove(id);
}
});
}(Tygh, Tygh.$));
</script>
{$filter_total=0}
{$filter_active=0}
<div id="ab_am_addons">
{if !empty($available_products.addons)}
{$addons=$available_products.addons}
<div>
<div class="ab-am-notes">
{$note_id='available_addons'}
{capture name="notes_picker"}{__('ab__am.addons_sets.notes') nofilter}{/capture}
{include file="common/popupbox.tpl" act="link" id="content_`$note_id`_notes" link_text=__("ab__am.note") text=__("note") content=$smarty.capture.notes_picker}
</div>
{$state=$smarty.cookies.ab__am_available_addons|default:'open'}
{include file="common/subheader.tpl" title=__("ab__am.available_addons") target="#ab__am_available_addons" meta="ab__am-section {if $state == 'close'}collapsed{/if}"}
<div id="ab__am_available_addons" class="{if $state == 'open'}in {/if}collapse" style="">
{include file="addons/ab__addons_manager/views/ab__am/components/addons.tpl" addons=$addons type='addons'}
</div>
</div>
<hr>
{/if}
{if !empty($available_products.sets)}
{$sets=$available_products.sets}
{capture name="sets"}
{foreach from=$sets key="s" item="set_group" }
{foreach from=$set_group key="s_key" item="set"}
{$filter_total=$filter_total+1}
{if $set.state == 'hide'}{continue}{/if}
{$filter_active=$filter_active+1}
{include file="addons/ab__addons_manager/views/ab__am/components/set.tpl" type='set'}
{/foreach}
{/foreach}
{/capture}
{if trim($smarty.capture.sets)}
<div>
<div class="ab-am-notes">
{$note_id='available_sets'}
{capture name="notes_picker"}{__('ab__am.addons_sets.notes') nofilter}{/capture}
{include file="common/popupbox.tpl" act="link" id="content_`$note_id`_notes" link_text=__("ab__am.note") text=__("note") content=$smarty.capture.notes_picker}
</div>
{$state=$smarty.cookies.ab__am_available_sets|default:'open'}
{include file="common/subheader.tpl" title=__("ab__am.available_sets") target="#ab__am_available_sets" meta="ab__am-section {if $state == 'close'}collapsed{/if}"}
<div id="ab__am_available_sets" class="{if $state == 'open'}in {/if}collapse" style="">
<table width="100%" class="table table-middle ab-am-table ab-am-set">
<thead>
<tr class="first-sibling">
<th width="60%" class="cm-non-cb">{__('ab__am.set.set')}</th>
<th width="25%" class="right cm-non-cb">&nbsp;</th>
<th width="25%" class="right cm-non-cb">{__('ab__am.addon.table_head.subscription')}</th>
</tr>
</thead>
{$smarty.capture.sets nofilter}
</table>
</div>
</div>
<hr>
{/if}
{/if}
<!--ab_am_addons--></div>
{else}
<p class="no-items">{__("ab__am.no_data", ['[domain]' => $config.http_host])}</p>
{/if}
{/capture}
{capture name="buttons"}
{if !empty($d)}
<div class="btn-group">
<input type="text"
name="q"
id="ab__am_search"
autofocus
class="input-full input--no-margin"
placeholder="{__("search")}"
autocomplete="off"
/>
<button type="button" class="hidden" id="ab__am_search__clear" title="{__("remove")}">
<i class="icon icon-remove"></i>
</button>
</div>
<div class="btn-group">
<a href="#" class="btn dropdown-toggle" data-toggle="dropdown" style="height: 32px;">
<span id="ab_am_filter">
{__("ab__am.filter", ['[active]' => $filter_active, '[total]' => $filter_total])}
<!--ab_am_filter--></span>
<span class="caret"></span>
</a>
<ul class="dropdown-menu ab-am-filter">
{if !empty($available_products.addons)}
{foreach $available_products.addons as $item}
{if $item.key == 'ab__addons_manager'}
<li>
<a href="" onclick="return false;">
<input type="checkbox" value="Y" checked="checked">{$item.name|truncate:50}
</a>
</li>
{else}
{$menu_item_id="ab_am_filter_addon_{$item@key}"}
<li id="{$menu_item_id}">
<a>
<label for="state_{$menu_item_id}">
<input onchange="Tygh.$.ceAjax('request', fn_url('ab__am.addons'), {$ldelim} data: {$ldelim} type: 'addon', key: '{$item@key}', state: this.checked ? 'show' : 'hide', {$rdelim}, result_ids: 'ab_am_addons,ab_am_filter,{$menu_item_id}'{$rdelim});" id="state_{$menu_item_id}" type="checkbox"{if $item.state == 'show'} checked="checked"{/if}>{$item.name|truncate:50} {__('ab__am.order', ['[order_id]' => $item.order_id])}
</label>
</a>
<!--{$menu_item_id}--></li>
{/if}
{/foreach}
{/if}
{if !empty($available_products.sets)}
{foreach $available_products.sets as $set_items}
{foreach $set_items as $item}
{$menu_item_id="ab_am_filter_set_{$set_items@key}_{$item@key}"}
<li id="{$menu_item_id}">
<a>
<label for="state_{$menu_item_id}">
<input onchange="Tygh.$.ceAjax('request', fn_url('ab__am.addons'), {$ldelim} data: {$ldelim} type: '{$set_items@key}', key: '{$item@key}', state: this.checked ? 'show' : 'hide', {$rdelim}, result_ids: 'ab_am_addons,ab_am_filter,{$menu_item_id}'{$rdelim});" id="state_{$menu_item_id}" type="checkbox"{if $item.state == 'show'} checked="checked"{/if}>{$item.name|truncate:50} {__('ab__am.order', ['[order_id]' => $item.order_id])}
</label>
</a>
<!--{$menu_item_id}--></li>
{/foreach}
{/foreach}
{/if}
</ul>
</div>
{/if}
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__addons_manager"}
{include file="common/mainbox.tpl" title=__('ab__am.addons')
adv_buttons=$smarty.capture.adv_buttons
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
sidebar=$smarty.capture.sidebar}