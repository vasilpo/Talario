{if $ab__stickers_output_settings}
{if $ab__stickers_output_settings.update_for_all && $settings.Stores.default_state_update_for_all == 'not_active' && !$runtime.simple_ultimate}
{assign var="disable_input" value=true}
{/if}
<div id="container_addon_option_ab__stickers_output_type" class="control-group setting-wide ab__stickers">
<label class="control-label cm-color" for="addon_option_ab__stickers_output_type">{__('ab__stickers.output_type')}{include file="common/tooltip.tpl" tooltip=__('ab__stickers.output_type.tooltip')}:</label>
<div class="select-field controls">
<div class="table-wrapper">
<table class="table table-middle table--relative table-bordered">
{include file="common/image.tpl" image_width="460" image_height="336"}
<tbody>
<tr>
<td width="45%" class="cm-tooltip stickers-L" title="{__('ab__stickers.output_position.TL')}">
<label for="ab__stickers_TL">{__('ab__stickers.output_type')}</>
<select class="input-small"{if $addons.ab__stickers.output_position == "R"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.TL_output_type.object_id}]" id="ab__stickers_TL">
{foreach ['column', 'row'] as $output_type}
<option value="{$output_type}"{if $output_type == $addons.ab__stickers.TL_output_type} selected{/if}>{__("ab__stickers.output_type.`$output_type`")}</option>
{/foreach}
</select>
<label for="ab__stickers_TL_max_count">{__('ab__stickers.max_count')}</label>
<select class="input-small"{if $addons.ab__stickers.output_position == "R"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.TL_max_count.object_id}]" id="ab__stickers_TL_max_count">
{for $max_count=1 to 10}
<option{if $addons.ab__stickers.TL_max_count == $max_count} selected{/if} value="{$max_count}">{$max_count}</option>
{/for}
</select>
</td>
<td></td>
<td width="45%" class="cm-tooltip stickers-R" title="{__('ab__stickers.output_position.TR')}">
<label for="ab__stickers_TR">{__('ab__stickers.output_type')}</label>
<select class="input-small"{if $addons.ab__stickers.output_position == "L"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.TR_output_type.object_id}]" id="ab__stickers_TR">
{foreach ['column', 'row'] as $output_type}
<option value="{$output_type}"{if $output_type == $addons.ab__stickers.TR_output_type} selected{/if}>{__("ab__stickers.output_type.`$output_type`")}</option>
{/foreach}
</select>
<label for="ab__stickers_TR_max_count">{__('ab__stickers.max_count')}</label>
<select class="input-small"{if $addons.ab__stickers.output_position == "L"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.TR_max_count.object_id}]" id="ab__stickers_TR_max_count">
{for $max_count=1 to 10}
<option{if $addons.ab__stickers.TR_max_count == $max_count} selected{/if} value="{$max_count}">{$max_count}</option>
{/for}
</select>
</td>
</tr>
<tr>
<td></td>
<td style="padding: 20px"></td>
<td></td>
</tr>
<tr>
<td class="cm-tooltip stickers-L" title="{__('ab__stickers.output_position.BL')}">
<label for="ab__stickers_BL">{__('ab__stickers.output_type')}</label>
<select class="input-small"{if $addons.ab__stickers.output_position == "R"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.BL_output_type.object_id}]" id="ab__stickers_BL">
{foreach ['column', 'row'] as $output_type}
<option value="{$output_type}"{if $output_type == $addons.ab__stickers.BL_output_type} selected{/if}>{__("ab__stickers.output_type.`$output_type`")}</option>
{/foreach}
</select>
<label for="ab__stickers_BL_max_count">{__('ab__stickers.max_count')}</label>
<select class="input-small"{if $addons.ab__stickers.output_position == "R"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.BL_max_count.object_id}]" id="ab__stickers_BL_max_count">
{for $max_count=1 to 10}
<option{if $addons.ab__stickers.BL_max_count == $max_count} selected{/if} value="{$max_count}">{$max_count}</option>
{/for}
</select>
</td>
<td></td>
<td class="cm-tooltip stickers-R" title="{__('ab__stickers.output_position.BR')}">
<label for="ab__stickers_BR">{__('ab__stickers.output_type')}</label>
<select class="input-small"{if $addons.ab__stickers.output_position == "L"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.BR_output_type.object_id}]" id="ab__stickers_BR">
{foreach ['column', 'row'] as $output_type}
<option value="{$output_type}"{if $output_type == $addons.ab__stickers.BR_output_type} selected{/if}>{__("ab__stickers.output_type.`$output_type`")}</option>
{/foreach}
</select>
<label for="ab__stickers_BR_max_count">{__('ab__stickers.max_count')}</label>
<select class="input-small"{if $addons.ab__stickers.output_position == "L"} disabled{/if} name="addon_data[options][{$ab__stickers_output_settings.BR_max_count.object_id}]" id="ab__stickers_BR_max_count">
{for $max_count=1 to 10}
<option{if $addons.ab__stickers.BR_max_count == $max_count} selected{/if} value="{$max_count}">{$max_count}</option>
{/for}
</select>
</td>
</tr>
</tbody>
</table>
</div>
<div class="right update-for-all">
{include file="buttons/update_for_all.tpl" display=$ab__stickers_output_settings.update_for_all object_id=$ab__stickers_output_settings.object_id name="update_all_vendors[`$ab__stickers_output_settings.object_id`]" hide_element="addon_option_ab__stickers_output_type"}
</div>
</div>
</div>
<script>
(function (_, $) {
$.ceEvent('on', 'ce.commoninit', function(context) {
context.find('select[id^="addon_option_ab__stickers_output_position"]').on('change', function(){
var $this = $(this);
var disable = $this.val() === 'L' ? 'R' : 'L';
context.find('.stickers-L select, .stickers-R select').removeAttr('disabled');
context.find('.stickers-' + disable + ' select').attr('disabled', '');
});
});
})(Tygh, Tygh.$);
</script>
{/if}