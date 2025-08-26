{include file="common/subheader.tpl" title=__("abt__unitheme2.settings")}
{foreach ['desktop','tablet','mobile'] as $device_type}
<div class="control-group">
{$name_var = "abt__ut2_display_`$device_type`"}
<label class="control-label" for="elm_filter_display_{$device_type}_{$id}">{__("abt__ut2.filter.display_`$device_type`")}</label>
<div class="controls">
<select name="filter_data[{$name_var}]" id="elm_filter_{$name_var}_{$id}">
<option value="Y" {if $filter.$name_var == 'Y'} selected="selected"{/if}>{__("expanded")}</option>
<option value="N" {if $filter.$name_var == 'N'} selected="selected"{/if}>{__("minimized")}</option>
</select>
</div>
</div>
{/foreach}
