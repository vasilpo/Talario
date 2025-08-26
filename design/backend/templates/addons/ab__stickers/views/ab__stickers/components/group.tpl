{assign var="prefix_md5" value=$prefix|md5}
<input type="hidden" name="{$prefix}[fake]" value="" disabled="disabled" />
<ul class="conditions-tree-group cm-row-item">
<li class="no-node{if $root}-root{/if} clearfix">
{include file="addons/ab__stickers/views/ab__stickers/components/conditions/group_header.tpl" hide_add_buttons=$hide_inputs group=$group prefix_md5=$prefix_md5 root=$root}
</li>
{foreach $group.conditions as $condition_data}
<li id="container_condition_{$prefix_md5}_{$condition_data@key}" class="cm-row-item{if $condition_data@last} cm-last-item{/if}">
{if $condition_data.set}
{include file="addons/ab__stickers/views/ab__stickers/components/conditions.tpl" prefix="`$prefix`[conditions][`$condition_data@key`]" elm_id="condition_`$prefix_md5`_`$condition_data@key`" root=false group=$condition_data}
{else}
{include file="addons/ab__stickers/views/ab__stickers/components/conditions/condition.tpl" prefix="`$prefix`[conditions][`$condition_data@key`]" elm_id="condition_`$prefix_md5`_`$condition_data@key`" condition_data=$condition_data}
{/if}
</li>
{foreachelse}
<li class="no-node no-items">
<p class="no-items">{__('no_items')}</p>
</li>
{/foreach}
{$conditions = fn_ab__stickers_form_conditions_optgroups($schema.conditions)}
<li id="container_add_condition_{$prefix_md5}" class="hidden cm-row-item">
<div class="conditions-tree-node">
<select onchange="Tygh.$.ceAjax('request', '{"ab__stickers.dynamic?&sticker_id=`$smarty.request.sticker_id`&type=`$sticker_type`"|fn_url nofilter}&prefix=' + encodeURIComponent(this.name) + '&condition=' + this.value + '&elm_id=' + this.id, {$ldelim}result_ids: 'container_' + this.id{$rdelim})" id="add_condition_{$prefix_md5}">
<option value=""> -- </option>
{foreach $conditions as $optgroup}
<optgroup label="{__($optgroup@key)}">
{foreach $optgroup as $condition}
<option value="{$condition@key}">{__("ab__stickers.conditions.names.`$condition@key`")}</option>
{/foreach}
</optgroup>
{/foreach}
</select>
</div>
</li>
</ul>