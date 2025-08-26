{if "ab__hpd.view"|fn_check_view_permissions && $addons.ab__hide_product_description.hide_in_product == "YesNo::YES"|enum}
{strip}
<div id="content_ab__smc_{$html_id}"{if !"ab__hpd.manage"|fn_check_view_permissions} class="cm-hide-inputs"{/if}>
<fieldset>
<div class="control-group">
<label class="control-label" for="elm_ab__smc_hide_content_{$html_id}">{__("ab__smc.product_tabs.hide_content")}:</label>
<div class="controls" id="elm_ab__smc_hide_content_{$html_id}">
{if $tab_data.ab__smc_hide_content|is_array}
<div class="btn-group btn-group-checkbox">
{foreach ["phone", "tablet", "desktop"] as $device}
<input type="hidden" name="tab_data[ab__smc_hide_content][{$device}]" value="{"YesNo::NO"|enum}">
<input type="checkbox" class="btn-group-checkbox__checkbox"{if $tab_data.ab__smc_hide_content.$device|default:{"YesNo::YES"|enum} == "YesNo::YES"|enum} checked{/if} id="elm_ab__smc_{$html_id}_hide_on_{$device}" name="tab_data[ab__smc_hide_content][{$device}]" value="{"YesNo::YES"|enum}">
<label class="btn btn-group-checkbox__label" for="elm_ab__smc_{$html_id}_hide_on_{$device}">
<i class="icon{if $device == "phone"}-mobile{/if}-{$device}"></i>&nbsp;
{__("block_manager.availability.`$device`")}
</label>
{/foreach}
</div>
{else}
<div class="alert alert-warning">{__("error_occurred")}</div>
{/if}
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_ab__smc_show_more_{$html_id}">{__("ab__smc.product_tabs.hide_content.more")}:</label>
<div class="controls">
<input type="text" name="tab_data[ab__smc_show_more]" id="elm_ab__smc_show_more_{$html_id}" value="{$tab_data.ab__smc_show_more}">
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_ab__smc_show_less_{$html_id}">{__("ab__smc.product_tabs.hide_content.less")}:</label>
<div class="controls">
<input type="text" name="tab_data[ab__smc_show_less]" id="elm_ab__smc_show_less_{$html_id}" value="{$tab_data.ab__smc_show_less}">
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_ab__smc_override_height_{$html_id}">{__("ab__smc.product_tabs.override")}
{include file="common/tooltip.tpl" tooltip=__("ab__smc.product_tabs.override.tooltip")}:</label>
<div class="controls">
<input type="hidden" name="tab_data[ab__smc_override]" value="N">
<input type="checkbox" name="tab_data[ab__smc_override]" id="elm_ab__smc_override_height_{$html_id}"{if $tab_data.ab__smc_override == "Y"} checked="checked"{/if} value="Y">
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_ab__smc_tab_height_{$html_id}">{__("ab__smc.product_tabs.height")}
{include file="common/tooltip.tpl" tooltip=__("ab__smc.product_tabs.height.tooltip")}:</label>
<div class="controls">
<input type="text" class="cm-value-integer" name="tab_data[ab__smc_height]" id="elm_ab__smc_tab_height_{$html_id}" value="{$tab_data.ab__smc_height|default:$addons.ab__hide_product_description.max_height}">
</div>
</div>
</fieldset>
</div>
{/strip}
{/if}