<div class="row-fluid">
<div class="group span6 form-horizontal">
<div class="control-group">
<label for="ab__stickers_name_for_desktop" class="control-label">{__('ab__stickers.name_for_desktop')}:</label>
<div class="controls">
<input type="text" name="name_for_desktop" id="ab__stickers_name_for_desktop" value="{$search.name_for_desktop}">
</div>
</div>
</div>
<div class="group span6 form-horizontal">
<div class="control-group">
<label for="ab__stickers_name_for_mobile" class="control-label">{__('ab__stickers.name_for_mobile')}:</label>
<div class="controls">
<input type="text" name="name_for_mobile" id="ab__stickers_name_for_mobile" value="{$search.name_for_mobile}">
</div>
</div>
</div>
</div>
<div class="row-fluid">
<div class="group span6 form-horizontal">
<div class="control-group">
<label for="ab__stickers_appearance_style" class="control-label">{__("ab__stickers.appearance_style")}:</label>
<div class="controls">
<select name="appearance_style" id="ab__stickers_appearance_style">
<option value="">---</option>
{foreach fn_ab__stickers_sticker_get_ts_appearance_styles() as $appearance_style}
<option value="{$appearance_style@key}"{if $search.appearance_style == $appearance_style@key} selected{/if}>
{$appearance_style}
</option>
{/foreach}
</select>
</div>
</div>
</div>
<div class="group span6 form-horizontal">
<div class="control-group">
<label for="ab__stickers_status" class="control-label">{__('status')}:</label>
<div class="controls">
<select name="status" id="ab__stickers_status">
<option value="">---</option>
<option{if $search.status == "A"} selected{/if} value="A">{__("active")}</option>
<option{if $search.status == "D"} selected{/if} value="D">{__("disabled")}</option>
</select>
</div>
</div>
</div>
</div>