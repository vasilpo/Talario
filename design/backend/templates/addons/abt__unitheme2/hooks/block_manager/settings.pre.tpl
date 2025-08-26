{if $runtime.layout.theme_name == 'abt__unitheme2'}
<div id="abt__ut2_block_settings_{$elm_id}">
<div class="abt-ut2-doc">{__('abt__ut2.block.availability.show_on',
['[show_on]' => "{__("block_manager.availability.show_on")}: {__("block_manager.availability.phone")}/{__("block_manager.availability.tablet")}/{__("block_manager.availability.desktop")}"])}</div>
{if $block.type != "main"}
<input type="hidden" name="block_data[properties][abt__ut2_demo_block_id]" value="{$block.properties.abt__ut2_demo_block_id}" />
{/if}
{$devices = $option.devices|default:['desktop','tablet','mobile']}
{$icons = [
"desktop" => "icon-desktop",
"mobile" => "icon-mobile-phone",
"tablet" => "icon-tablet"
]}
{include file="common/subheader.tpl" meta="collapsed" title=__("abt__ut2.block.skeleton.title") target="#abt__ut2_skeleton_settings_{$html_id}"}
<div id="abt__ut2_skeleton_settings_{$html_id}" class="collapse">
{if $block|fn_abt__ut2_is_block_lazy_load_allowed}
<p>
{__("abt__ut2.block.skeleton.description")}
</p>
<div class="control-group">
<label class="control-label" style="margin-top: 28px;">{__("abt__ut2.block.skeleton.height")}:</label>
<div class="controls">
<table>
<thead>
<tr>
{foreach $devices as $device}
{$tr = "abt__ut2.block.skeleton.height_`$device`"}
<th><label for="skeleton_height_{$device}"><i class="{$icons.$device}"></i> {__($tr)}</th>
{/foreach}
</tr>
</thead>
<tbody>
<tr>
{foreach $devices as $device}
{$elem = "skeleton_height_`$device`"}
<td>
<input id="skeleton_height_{$device}" name="block_data[properties][{$elem}]" type="text" class="input-medium cm-value-integer" value="{$block.properties.$elem}">
</td>
{/foreach}
</tr>
</tbody>
</table>
</div>
</div>
{else}
<div class="abt-ut2-doc">
{__("abt__ut2.block.skeleton.not_allowed_for_this_block")}
</div>
{/if}
</div>
</div>
{/if}
