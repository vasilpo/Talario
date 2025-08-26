{if !$root}
<div class="pull-right">
<a class="icon-trash cm-delete-row cm-tooltip conditions-tree-remove" name="remove" id="{$item_id}" title="{__("remove_this_item")}"></a>
</div>
{/if}
<div id="add_condition_{$prefix_md5}" class="btn-toolbar pull-right">
{if !$hide_add_buttons}
{include file="common/tools.tpl" hide_tools=true tool_onclick="Tygh.ab__stickers.functions.add_condition(Tygh.$(this).parents('div[id^=add_condition_]').prop('id'), false, 'condition')" prefix="simple" link_text=__("add_condition")}
{*{include file="common/tools.tpl" hide_tools=true tool_onclick="Tygh.ab__stickers.functions.add_group(Tygh.$(this).parents('div[id^=add_condition_]').prop('id'), `$smarty.request.sticker_id`)" prefix="simple" link_text=__("add_group")}*}
{/if}
</div>
{capture name="set"}
{if $group.set == "any"}
{assign var="selected_name" value=__("ab__stickers.conditions.any")}
{else}
{assign var="selected_name" value=__("ab__stickers.conditions.all")}
{/if}
{if $hide_add_buttons}
{$selected_name}
{else}
{include file="common/select_object.tpl" style="field" items=["all" => __("ab__stickers.conditions.all"), "any" => __("ab__stickers.conditions.any")] select_container_name="`$prefix`[set]" selected_key=$group.set selected_name=$selected_name}
{/if}
{/capture}
{capture name="set_value"}
{if $sticker_type != $dynamic_type}
{if !$group || $group.set_value}
{assign var="selected_name" value=__("ab__stickers.conditions.true")}
{else}
{assign var="selected_name" value=__("ab__stickers.conditions.false")}
{/if}
{if $hide_add_buttons}
{$selected_name}
{else}
{include file="common/select_object.tpl" style="field" items=["0" => __("ab__stickers.conditions.false"), "1" => __("ab__stickers.conditions.true")] select_container_name="`$prefix`[set_value]" selected_key=$group.set_value|default:1 selected_name=$selected_name}
{/if}
{else}
{__("ab__stickers.conditions.true")}
{/if}
{/capture}
{__('ab__stickers.group_conditions', ["[set]" => $smarty.capture.set, "[set_value]" => $smarty.capture.set_value])}