<table class="table table-middle ab-am-table ab-am-addons no-hover" style="margin-bottom: 0;">
<thead>
<tr>
<th width="1%"></th>
<th width="40%">{__('ab__am.addon.table_head.addon')}</th>
<th width="1%" class="center" nowrap>&nbsp;</th>
<th width="15%" class="center" nowrap>{__('ab__am.addon.table_head.versions')}{include file="common/tooltip.tpl" tooltip="{__('ab__am.addon.table_head.versions.tooltip')}"}</th>
<th width="20%" class="left">{__('ab__am.addon.table_head.actions')}</th>
{if $type != 'set'}
<th width="20%" class="left">{__('ab__am.addon.table_head.subscription')}</th>
{/if}
</tr>
</thead>
{$iteration = 0}
{foreach from=$addons item="a" key="k_a" name="a"}
{$ab__am = $a.key=='ab__addons_manager'}
{if !$a.key}{continue}{/if}
{$filter_total=$filter_total+1 scope="parent"}
{if $type == 'addons' && $a.state == 'hide'}
{continue}
{/if}
{$filter_active=$filter_active+1 scope="parent"}
{$iteration = $iteration + 1}
<tbody>
<tr>
<td class="pd-nr">
<span class="iteration">{$iteration}.&nbsp;</span>
<span class="ab_logo {$cs_addons[$a.key].status} cm-tooltip hand" title="{__("ab__am.addon.status.{$cs_addons[$a.key].status|default:'n'}")}">&nbsp;</span>
{if $a.action.status == 'update_addon'}
<span title="{__("ab__am.menu.available_update")}" class="ab-am-available-updates"></span>
{/if}
</td>
<td class="left_border">
<span class="ab-am-addon-name">
<span style="display: none">{$a.key}</span>
{if $ab__am}{$a.name}{else}<a href="{$a.url}" target="_blank">{$a.name}</a>{/if}
{if $type != 'set' and !$ab__am}&nbsp;{__('ab__am.order', ['[order_id]' => $a.order_id])}{/if}
</span>
{if $events.available_updates[$a.key]}
<span class="ab-am-new-version hand cm-tooltip" title="{__('ab__am.addon.new_version_is_available.tooltip', ['[ver]' => $events.available_updates[$a.key], '[date]' => $all_products.addons[$a.key].build.t|date_format:"`$settings.Appearance.date_format`"])}">
{__('ab__am.addon.new_version_is_available', ['[ver]' => $events.available_updates[$a.key], '[date]' => $all_products.addons[$a.key].build.t|date_format:"`$settings.Appearance.date_format`"])}
</span>
{/if}
{if $a.description or $a.doc}
<p class="ab-am-addon-description">
{if !$ab__am}
<a target="_blank" href="{$a.url}?selected_section=changelog#changelog">{__('ab__am.addon.changelog')}</a>
{if $a.doc}
<span class="separate">|</span>
{/if}
{/if}
{if $a.doc}
<a target="_blank" href="{$a.doc}">{__('ab__am.addon.doc')}</a><br>
{/if}
{if $a.description}{$a.description}{/if}
</p>
{/if}
</td>
<td width="9%" class="nowrap">
{if !$ab__am}
{$menu_items = fn_ab__am_get_menu($a.key)}
{if $menu_items}
<div class="hidden-tools">
{capture name="tools_list"}
{foreach $menu_items as $mi}
{if $mi['divider']}
<li class="divider"></li>
{else}
<li>{btn type="list" text=$mi.text href="{$mi.href}" target="_blank"}</li>
{/if}
{/foreach}
{/capture}
{dropdown content=$smarty.capture.tools_list}
</div>
{/if}
{elseif $runtime.action == 'tools'}
<div class="hidden-tools">
{capture name="tools_list"}
<li>{include file="common/popupbox.tpl" id="ab__am_tools_change_licenses" text=__("ab__am.tools.change_licenses") link_text=__("ab__am.tools.change_licenses") act="link" href="ab__am.tools.change_licenses"}</li>
<li>{include file="common/popupbox.tpl" id="ab__am_tools_fix_licenses" text=__("ab__am.tools.fix_licenses") link_text=__("ab__am.tools.fix_licenses") act="link" href="ab__am.tools.fix_licenses"}</li>
{/capture}
{dropdown content=$smarty.capture.tools_list}
</div>
{/if}
</td>
<td class="left_border right">
{strip}
{foreach ['installed', 'available', 'final'] as $version_type}
{__("ab__am.addon.versions.`$version_type`")}:&nbsp;
{if !$a.builds[$version_type]}&mdash;
{else}
v{$a.builds[$version_type].version}
{include file="common/tooltip.tpl" tooltip=__('ab__am.addon.version_info', [
'[date]' => $a.builds[$version_type].timestamp|date_format:"`$settings.Appearance.date_format`",
'[ultimate]' => $a.builds[$version_type].ultimate|default:'-',
'[multivendor]' => $a.builds[$version_type].multivendor|default:'-'
])}
{/if}
<br>
{/foreach}
{/strip}
</td>
<td class="left_border left">
{if $a.action.status == 'install_addon' && !in_array($cs_addons[$a.key].status, ['A','D'])}
<span class="ab-am-action {$a.action.status}">{__("ab__am.addon.action.`$a.action.status`", ['[ver]' => $a.action.version])}</span>
<br><br>
{if $a.action.ab__am}<span class="ab-am-action update_am">{__("ab__am.addon.action.require_update", ['[ver]' => $a.action.ab__am.version])}</span>{else}
<form action="{""|fn_url}" name="ab_install_form_{$k_a}" method="post" class="cm-ajax cm-comet cm-disable-check-changes">
<input type="hidden" name="dispatch" value="ab__am.install" />
<input type="hidden" name="ab_code" value="{$a.code}">
<input type="hidden" name="result_ids" value="ab_am_addons,header_subnav">
<input type="hidden" name="key" value="{$a.key}">
<input type="hidden" name="name" value="{$a.name}">
<input class="btn cm-confirm cm-tooltip ab-am-action-button-install_addon" type="submit"
value="{__("ab__am.addon.action.`$a.action.status`.button.value", ['[ver]' => $a.action.version])}"
data-ca-confirm-text="{__("ab__am.addon.action.`$a.action.status`.button.tooltip", ['[ver]' => $a.action.version, '[name]' => $a.name])|replace:"'":'"'}"
title="{__("ab__am.addon.action.`$a.action.status`.button.tooltip", ['[ver]' => $a.action.version, '[name]' => $a.name])|replace:'"':"'"}" />
</form>
{/if}
{elseif $a.action.status == 'update_addon' && preg_match('#^([0-9]{1,2})[.]([0-9]{1,2})[.]([0-9]{1,2})|([0-9]{1,2})[.]([0-9]{1,2})[.]([0-9]{1,2})[.]([a-z]{1})$#', $a.builds.installed.version)}
<span class="ab-am-action {$a.action.status}">{__("ab__am.addon.action.`$a.action.status`", ['[ver]' => $a.action.version])}</span><br><br>
{if $cs_addons[$a.key].status == 'A'}
{if $a.action.ab__am}<span class="ab-am-action update_am">{__("ab__am.addon.action.require_update", ['[ver]' => $a.action.ab__am.version])}</span>{else}
{btn type="text" class="cm-tooltip btn ab-am-action-button-install_addon" href="upgrade_center.refresh" target="_blank"
text=__("ab__am.addon.action.`$a.action.status`.button.value", ['[ver]' => $a.action.version, '[name]' => $a.name])
title=__("ab__am.addon.action.`$a.action.status`.button.tooltip", ['[ver]' => $a.action.version, '[name]' => $a.name])
}
{/if}
{/if}
{elseif $a.action.status == 'wait_new_version'}
<span class="ab-am-action {$a.action.status}">{__("ab__am.addon.action.`$a.action.status`", ['[ver]' => $a.action.version, '[cscart]' => $a.action.cscart])}</span>
{elseif $a.action.status == 'not_tested_yet'}
<span class="ab-am-action {$a.action.status}">{__("ab__am.addon.action.`$a.action.status`", ['[ver]' => $a.action.version, '[cscart]' => $a.action.cscart])}</span>
{elseif $a.action.status == 'unavailable_addon'}
<span class="ab-am-action {$a.action.status}">{__("ab__am.addon.action.`$a.action.status`", ['[cscart]' => $a.action.cscart])}</span>
{/if}
</td>
{if $type != 'set'}
<td class="left_border left">
{if $a.key != 'ab__addons_manager'}
<div class="ab-am-subscription {$a.subscription.status}">{__("ab__am.addon.subscription.`$a.subscription.status`", ['[date]' => $a.subscription.date|date_format:"`$settings.Appearance.date_format`"])}
<br>
<br>
{btn type="text" text=__("ab__am.addon.subscription.`$a.subscription.status`.button.value") title=__("ab__am.addon.subscription.`$a.subscription.status`.button.tooltip") class="cm-tooltip btn btn-primary btn-{$a.subscription.status}" href="`$a.link`" target="_blank"}
</div>
{/if}
</td>
{/if}
</tr>
</tbody>
{/foreach}
</table>