{$device_prefix=''}
{if $device}
{$device_prefix="_`$device`"}
<div class="abt-ut2-doc">{__('abt__ut2.banner.warning')}</div>
{/if}
{if !empty($banner.banner_id)}
<div class="control-group">
<label for="elm_banner_preview" class="control-label">{__('abt__ut2.banners.preview')}</label>
<div class="controls">
<a href="{"banners.preview&banner_id=`$banner.banner_id`&device=`$device`"|fn_url}" target="_blank" class="btn btn-primary">{__('abt__ut2.banners.view_preview')}</a>
</div>
</div>
{/if}
<h4 class="ty-subheader">{__("abt__ut2.banner.params_of_block")}</h4>
{$field="color_scheme"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach ['light', 'dark'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="content_valign"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
<option value="top" {if $banner.$elm == 'top'}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.top")}</option>
<option value="center" {if $banner.$elm == 'center' || !$banner.$elm} selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.center")}</option>
<option value="bottom" {if $banner.$elm == 'bottom'}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.bottom")}</option>
</select>
</div>
</div>
{$field="content_align"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
<option value="left" {if $banner.$elm == 'left'}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.left")}</option>
<option value="center" {if $banner.$elm == 'center' || !$banner.$elm}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.center")}</option>
<option value="right" {if $banner.$elm == 'right'}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.right")}</option>
</select>
</div>
</div>
{$field="padding"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{$explode_pd = explode(" ", $banner.$elm|default:"")}
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls cm-trim abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<span style="opacity: 0.5">{__("abt__ut2.banner.params.padding.label.top")}:</span>
<input type="text" name="pd_top_{$elm}" id="pd_top_{$elm}" value="{$explode_pd.0|default:"20px"}" size="5"/>
<span style="opacity: 0.5">{__("abt__ut2.banner.params.padding.label.right")}:</span>
<input type="text" name="pd_right_{$elm}" id="pd_right_{$elm}" value="{$explode_pd.1|default:"20px"}" size="5"/>
<span style="opacity: 0.5">{__("abt__ut2.banner.params.padding.label.bottom")}:</span>
<input type="text" name="pd_bottom_{$elm}" id="pd_bottom_{$elm}" value="{$explode_pd.2|default:"20px"}" size="5"/>
<span style="opacity: 0.5">{__("abt__ut2.banner.params.padding.label.left")}:</span>
<input type="text" name="pd_left_{$elm}" id="pd_left_{$elm}" value="{$explode_pd.3|default:"20px"}" size="5"/>
<input type="hidden" name="banner_data[{$elm}]" id="elm_banner_{$elm}" size="25"/>
<span class="muted description">{__("abt__ut2.banner.params.padding.label")}</span><br>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
const pd_top = document.getElementById('pd_top_{$elm}');
const pd_right = document.getElementById('pd_right_{$elm}');
const pd_bottom = document.getElementById('pd_bottom_{$elm}');
const pd_left = document.getElementById('pd_left_{$elm}');
const combinedValues = document.getElementById('elm_banner_{$elm}');
function updateHiddenInput() {
combinedValues.value = pd_top.value + " " + pd_right.value + " " + pd_bottom.value + " " + pd_left.value;
}
pd_top.addEventListener('input', updateHiddenInput);
pd_right.addEventListener('input', updateHiddenInput);
pd_bottom.addEventListener('input', updateHiddenInput);
pd_left.addEventListener('input', updateHiddenInput);
updateHiddenInput();
});
</script>
{$field="content_full_width"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="hidden" name="banner_data[{$elm}]" value="N"/>
<input type="checkbox" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="Y" {if $banner.$elm == "YesNo::YES"|enum}checked="checked"{/if}/>
</div>
</div>
<hr>
<h4 class="ty-subheader">{__("abt__ut2.banner.params_of_title")}</h4>
{$field="title"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls cm-trim abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="text" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="{$banner.$elm}" size="25" class="span10"/>
<p class="muted description">
<span style="opacity: 0.7">{__("abt__banner_title_presets")}:</span><br>
{foreach [__("abt__ut2.banner.pre_title") => '<small>Pre...</small>Title ...', __("abt__ut2.banner.another_color") => '<span style="color: red">Text..</span>', __("abt__ut2.banner.thin_font") => '<i>Text..</i>', __("abt__ut2.banner.new_row") => '<br>']
as $classes}
<input type="checkbox"
id="{$name}_{$classes@key}"
{if $banner.$elm && $banner.$elm|strpos:$classes !== false}checked="checked"{/if}
class="cm-text-toggle btn-group-checkbox__checkbox"
data-ca-toggle-text="{$classes}"
data-ca-toggle-text-target-elem-id="elm_banner_{$elm}"
/>
<label class="btn btn-group-checkbox__label" for="{$name}_{$classes@key}">
{$classes@key}
</label>
{/foreach}
</p>
</div>
</div>
{$field="title_font_size"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls cm-trim abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="text" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="{$banner.$elm|default:"24px"}" size="25" class="input-mini"/>
</div>
</div>
{$field="title_color"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="addons/abt__unitheme2/views/banners/components/colorpicker.tpl"}
</div>
</div>
{$field="title_font_weight"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach ['900', '700', '400', '300'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="title_tag"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach ['div', 'h1', 'h2', 'h3'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="title_shadow"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="hidden" name="banner_data[{$elm}]" value="N"/>
<input type="checkbox" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="Y" {if $banner.$elm == "YesNo::YES"|enum}checked="checked"{/if}/>
</div>
</div>
<hr>
<h4 class="ty-subheader">{__("abt__ut2.banner.params_of_description")}</h4>
{$field="description"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls cm-trim abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<textarea id="elm_banner_{$elm}" name="banner_data[{$elm}]" cols="35" rows="6" class="span10">{$banner.$elm}</textarea>
</div>
</div>
{$field="description_font_size"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls cm-trim abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="text" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="{$banner.$elm|default:"16px"}" size="25" class="input-small"/>
</div>
</div>
{$field="description_color"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="addons/abt__unitheme2/views/banners/components/colorpicker.tpl"}
</div>
</div>
{$field="description_bg_color"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="addons/abt__unitheme2/views/banners/components/colorpicker.tpl"}
</div>
</div>
<hr/>
<h4 class="ty-subheader">{__("abt__ut2.banner.internal_content")}</h4>
{** Control the type of internal image **}
<script>
function fn_change_object_type{$device_prefix}(v) {
var img_obj = $('.control-group.object-image{$device_prefix}');
var vd_obj = $('.control-group.object-video{$device_prefix}');
var pr_obj = $('.control-group.object-products{$device_prefix}');
switch (v){
case 'image':
img_obj.removeClass('hidden');
vd_obj.addClass('hidden');
pr_obj.addClass('hidden');
break;
case 'video':
img_obj.addClass('hidden');
vd_obj.removeClass('hidden');
pr_obj.addClass('hidden');
break;
case 'products':
img_obj.addClass('hidden');
vd_obj.addClass('hidden');
pr_obj.removeClass('hidden');
fn_abt__ut2_change_products_template(document.getElementById('elm_banner_abt__ut2_products_template'))
break;
}
}
</script>
{$field="object"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}" onchange="fn_change_object_type(this.value);">
{foreach ["image", "video", "products"] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="main_image"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'image' && $banner.banner_id} hidden{/if} object-image{$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<div id="elm_banner_{$elm}">
{include file="common/attach_images.tpl" image_name=$elm image_object_type="abt__ut2_banners" image_type="ImagePairTypes::MAIN"|enum image_pair=$banner.$elm image_object_id=$banner.abt__ut2_banner_image_id no_detailed=true hide_titles=true}
</div>
</div>
</div>
{$field="youtube_id"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'video'} hidden{/if} object-video{$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label cm-trim {$elm}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="text" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="{$banner.$elm}" size="25" class="span4"/>
<div class="img" style="margin-top: 10px">
{if strlen($banner.$elm)}
{if fn_abt__ut2_check_youtube_id($banner.$elm)}
<img src="https://img.youtube.com/vi/{$banner.$elm}/mqdefault.jpg" style="width: 160px;" alt="{$banner.$elm}">
{else}
<span class="alert">{__('Error')}</span>
{/if}
{/if}
</div>
</div>
</div>
{$field="youtube_autoplay"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
{*Value 0 (default): The video will not play automatically when the player loads.*}
{*Value 1: The video will play automatically when the player loads.*}
<div class="control-group{if $banner.abt__ut2_object != 'video'} hidden{/if} object-video{$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="hidden" name="banner_data[{$elm}]" value="N"/>
<input type="checkbox" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="Y" {if $banner.$elm == "YesNo::YES"|enum}checked="checked"{/if}/>
</div>
</div>
{$field="youtube_hide_controls"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
{*Value 0: Player controls does not display.*}
{*Value 1 (default): Player controls display.*}
<div class="control-group{if $banner.abt__ut2_object != 'video'} hidden{/if} object-video{$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="hidden" name="banner_data[{$elm}]" value="N"/>
<input type="checkbox" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="Y" {if $banner.$elm == "YesNo::YES"|enum}checked="checked"{/if}/>
</div>
</div>
{$field="products_template"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'products'} hidden{/if} object-products{$device_prefix}">
<label for="elm_banner_{$elm}_{$field}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}" onchange="fn_abt__ut2_change_products_template(this)">
<option value="grid_items"{if $banner.$elm === "grid_items"} selected{/if}>{__("abt__ut2.banner.{$field}.grid_items")}</option>
<option value="small_items"{if $banner.$elm === "small_items"} selected{/if}>{__("abt__ut2.banner.{$field}.small_items")}</option>
<option value="links_thumb"{if $banner.$elm === "links_thumb"} selected{/if}>{__("abt__ut2.banner.{$field}.links_thumb")}</option>
</select>
</div>
</div>
{$field="products_links_thumb_columns"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'products' || $banner.abt__ut2_products_template != 'links_thumb'} hidden{/if} object-products links_thumb{$device_prefix}">
<label for="elm_banner_{$elm}_{$field}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{for $i = 2 to 12}
<option value="{$i}"{if $banner.$elm == $i} selected{/if}>{$i}</option>
{/for}
</select>
</div>
</div>
{$field="products_links_thumb_rows"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'products' || $banner.abt__ut2_products_template != 'links_thumb'} hidden{/if} object-products links_thumb{$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
<option value="0"{if $banner.$elm == 0} selected{/if}>{__("abt__ut2.banner.params.{$field}.all")}</option>
<option value="1"{if $banner.$elm == 1} selected{/if}>{__("abt__ut2.banner.params.{$field}.one")}</option>
</select>
</div>
</div>
{$field="products_small_items_columns"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'products' || $banner.abt__ut2_products_template != 'small_items'} hidden{/if} object-products small_items{$device_prefix}">
<label for="elm_banner_{$elm}_{$field}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{for $i = 1 to 10}
<option value="{$i}"{if $banner.$elm == $i} selected{/if}>{$i}</option>
{/for}
</select>
</div>
</div>
{$field="products_small_items_rows"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'products' || $banner.abt__ut2_products_template != 'small_items'} hidden{/if} object-products small_items{$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
<option value="0"{if $banner.$elm == 0} selected{/if}>{__("abt__ut2.banner.params.{$field}.all")}</option>
<option value="1"{if $banner.$elm == 1} selected{/if}>{__("abt__ut2.banner.params.{$field}.one")}</option>
</select>
</div>
</div>
{$field="products_grid_columns"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'products' || $banner.abt__ut2_products_template != 'grid_items'} hidden{/if} object-products grid_items{$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{for $i = 1 to 10}
<option value="{$i}"{if $banner.$elm == $i} selected{/if}>{$i}</option>
{/for}
</select>
</div>
</div>
{$field="products_list"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.abt__ut2_object != 'products' } hidden{/if} object-products {$device_prefix}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include
file="views/products/components/picker/picker.tpl"
item_ids=","|explode:$banner.$elm
multiple=true
view_mode="external"
show_positions=true
allow_clear=true
for_current_storefront=true
picker_id="elm_banner_{$elm}"
input_name="banner_data[{$elm}]"
}
</div>
<hr>
</div>
<div class="control-group">
{$field="image_v_position"}
{$elm="abt__ut2`$device_prefix`_`$field`"}
{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group abt__ut2_vertical_image_align">
{if !$banner.$elm}
{$banner.$elm = "center"}
{/if}
<label for="elm_banner_{$elm}" class="control-label{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls btn-group abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{foreach ['top', 'center', 'bottom'] as $e}
{assign var="radio_id" value="elm_banner_{$elm}_{$e}"}
<label for="{$radio_id}" class="btn"><i class="ut2-icon-align-vertical-{$e} cm-tooltip" title="{__("abt__ut2.banner.params.{$field}.variants.{$e}")}"></i><input type="radio" name="banner_data[{$elm}]" id="{$radio_id}" value="{$e}" {if $banner.$elm == $e}checked="checked"{/if} /></label>
{/foreach}
</div>
</div>
{$field="image_h_position"}
{$elm="abt__ut2`$device_prefix`_`$field`"}
{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group abt__ut2_horizontal_image_align">
{if !$banner.$elm}
{$banner.$elm = "center"}
{/if}
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls btn-group abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{foreach ['left', 'center', 'right'] as $e}
{assign var="radio_id" value="elm_banner_{$elm}_{$e}"}
<label for="{$radio_id}" class="btn"><i class="ut2-icon-align-horizontal-{$e} cm-tooltip" title="{__("abt__ut2.banner.params.{$field}.variants.{$e}")}"></i><input type="radio" name="banner_data[{$elm}]" id="{$radio_id}" value="{$e}" {if $banner.$elm == $e}checked="checked"{/if} /></label>
{/foreach}
</div>
</div>
</div>
<h4 class="ty-subheader">{__("abt__ut2.banner.background")}</h4>
{** Control the type of background image **}
{$field="background_type"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
{$bg_type_field = $elm}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}" onchange="fn_ab__ut2_change_bg_type(this)">
<option value="image"{if $banner.{$bg_type_field}|default:"image" == "image"} selected{/if}>{__("ab__ut2.banners.background_type.image")}</option>
<option value="mp4_video"{if $banner.{$bg_type_field} == "mp4_video"} selected{/if}>{__("ab__ut2.banners.background_type.mp4_video")}</option>
</select>
</div>
</div>
<script>
function fn_ab__ut2_change_bg_type(elem)
{
var val = elem.value;
var clear_id_part = elem.id.slice(0, -('background_type'.length));
var image_wrap = $('#' + clear_id_part + 'background_image').parents('.control-group');
var mp4_wrap = $('#' + clear_id_part + 'background_mp4_video').parents('.control-group');
var bg_image_size = $('#' + clear_id_part + 'background_image_size').parents('.control-group');
var bg_image_position = $('#' + clear_id_part + 'background_image_position').parents('.control-group');
if (val === 'image') {
image_wrap.removeClass('hidden');
mp4_wrap.addClass('hidden');
bg_image_size.removeClass('hidden');
bg_image_position.removeClass('hidden');
} else if (val === 'mp4_video') {
image_wrap.addClass('hidden');
mp4_wrap.removeClass('hidden');
bg_image_size.addClass('hidden');
bg_image_position.addClass('hidden');
}
}
function fn_abt__ut2_change_products_template(e){
let val = e.value
const changes = {
grid_items : "small_items, .object-products.links_thumb",
small_items: "links_thumb, .object-products.grid_items",
links_thumb: "small_items, .object-products.grid_items"
}
$('.object-products.' + val).removeClass('hidden');
$('.object-products.' + changes[val]).addClass('hidden');
}
</script>
{$field="background_image"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.{$bg_type_field}|default:"image" != "image"} hidden{/if}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<div id="elm_banner_{$elm}">
{include file="common/attach_images.tpl" image_name=$elm image_object_type="abt__ut2_banners" image_type="ImagePairTypes::ADDITIONAL"|enum image_pair=$banner.$elm image_object_id=$banner.abt__ut2_banner_image_id no_detailed=true hide_alt=true hide_titles=true}
</div>
</div>
</div>
{$field="background_mp4_video"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner.{$bg_type_field}|default:"image" != "mp4_video"} hidden{/if}">
<label class="control-label">{__("abt__ut2.banner.params.background_mp4_video")}:</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="elm_banner_{$elm}" class="controls">
{if $banner.$elm}
<div style="width: 100%;" class="upload-file-section">
<p>
{$video_path = urlencode($banner.$elm)}
<a href="{"banners.delete_ut2_video?video_path=$video_path&banner_id=`$banner.banner_id`&type=$elm&redirect_url={$config.current_url|urlencode}"|fn_url}" class="image-delete cm-confirm cm-post delete cm-delete-image-link cm-tooltip" title="{__("delete")}"><span class="ty-icon ty-icon-cancel-circle"></span></a>
<span class="upload-filename">{$banner.$elm}</span>
</p>
</div>
{/if}
{include file="common/fileuploader.tpl" var_name="`$elm`[0]" prefix=$elm allowed_ext="mp4"}
</div>
</div>
{$field="background_image_size"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach ['cover', 'contain'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="background_color"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="addons/abt__unitheme2/views/banners/components/colorpicker.tpl"}
</div>
</div>
<div class="control-group">
{$field="background_v_position"}
{$elm="abt__ut2`$device_prefix`_`$field`"}
{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group abt__ut2_vertical_background_align">
{if !$banner.$elm}
{$banner.$elm = "center"}
{/if}
<label for="elm_banner_{$elm}" class="control-label{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls btn-group abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{foreach ['top', 'center', 'bottom'] as $e}
{assign var="radio_id" value="elm_banner_{$elm}_{$e}"}
<label for="{$radio_id}" class="btn"><i class="ut2-icon-align-vertical-{$e} cm-tooltip" title="{__("abt__ut2.banner.params.{$field}.variants.{$e}")}"></i><input type="radio" name="banner_data[{$elm}]" id="{$radio_id}" value="{$e}" {if $banner.$elm == $e}checked="checked"{/if} /></label>
{/foreach}
</div>
</div>
{$field="background_h_position"}
{$elm="abt__ut2`$device_prefix`_`$field`"}
{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group abt__ut2_horizontal_background_align">
{if !$banner.$elm}
{$banner.$elm = "center"}
{/if}
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls btn-group abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{foreach ['left', 'center', 'right'] as $e}
{assign var="radio_id" value="elm_banner_{$elm}_{$e}"}
<label for="{$radio_id}" class="btn"><i class="ut2-icon-align-horizontal-{$e} cm-tooltip" title="{__("abt__ut2.banner.params.{$field}.variants.{$e}")}"></i><input type="radio" name="banner_data[{$elm}]" id="{$radio_id}" value="{$e}" {if $banner.$elm == $e}checked="checked"{/if} /></label>
{/foreach}
</div>
</div>
</div>
<hr>
<h4 class="ty-subheader">{__("abt__ut2.banner.params_of_button")}</h4>
{** Buttons control **}
<script language="javascript">
function fn_button_use{$device_prefix}(el) {
if (!el.checked){
Tygh.$('#abt__ut2{$device_prefix}_button_text,#abt__ut2{$device_prefix}_button_text_color,#abt__ut2{$device_prefix}_button_color,#abt__ut2{$device_prefix}_button_style').addClass('hidden');
}else{
Tygh.$('#abt__ut2{$device_prefix}_button_text,#abt__ut2{$device_prefix}_button_text_color,#abt__ut2{$device_prefix}_button_color,#abt__ut2{$device_prefix}_button_style').removeClass('hidden');
}
}
</script>
{$field="button_use"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="hidden" name="banner_data[{$elm}]" value="N"/>
<input type="checkbox" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="Y" {if $banner.$elm == "YesNo::YES"|enum}checked="checked"{/if} onclick="fn_button_use{$device_prefix}(this);"/>
</div>
</div>
{$field="button_text"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner["abt__ut2`$device_prefix`_button_use"] == 'N'} hidden{/if}" id="{$elm}">
<label for="elm_banner_{$elm}" class="control-label cm-trim">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="text" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="{$banner.$elm}" size="25"/>
</div>
</div>
{$field="button_style"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
{$button_style_type_field = $elm}
<div class="control-group{if $banner["abt__ut2`$device_prefix`_button_use"] == 'N'} hidden{/if}" id="{$elm}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach ['normal', 'outline', 'text'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="button_text_color"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner["abt__ut2`$device_prefix`_button_use"] == 'N'} hidden{/if}" id="{$elm}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="addons/abt__unitheme2/views/banners/components/colorpicker.tpl"}
</div>
</div>
{$field="button_color"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group{if $banner["abt__ut2`$device_prefix`_button_use"] == 'N'} hidden{/if}" id="{$elm}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="addons/abt__unitheme2/views/banners/components/colorpicker.tpl"}
</div>
</div>
<hr>
<h4 class="ty-subheader">{__("abt__ut2.banner.params_additional")}</h4>
{$field="content_bg"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
{$content_bg = $banner.$elm}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}" onchange="fn_change_content_bg($(this));">
{foreach ['none', 'transparent', 'transparent_blur', 'transparent_gradient', 'colored'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="content_bg_position"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group {$field} {if !in_array($content_bg, ['transparent', 'transparent_blur', 'transparent_gradient', 'colored'])}hidden{/if}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach ['only_under_content', 'full_height', 'whole_banner'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="content_bg_align"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group {$field} {if !in_array($content_bg, ['transparent_gradient'])}hidden{/if}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach ['auto', 'left_to_right', 'right_to_left', 'top_to_bottom', 'bottom_to_top', 'center'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{$field="content_bg_opacity"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group {$field} {if !in_array($content_bg, ['transparent', 'transparent_blur', 'transparent_gradient'])}hidden{/if}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach range(5, 100, 5) as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{$e}%</option>
{/foreach}
</select>
</div>
</div>
{$field="content_bg_color"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group {$field} {if !in_array($content_bg, ['transparent', 'transparent_blur', 'transparent_gradient', 'colored'])}hidden{/if}">
<label for="elm_banner_{$elm}" class="control-label">{__("abt__ut2.banner.params.{$field}")}{include file="common/tooltip.tpl" tooltip=__("abt__ut2.banner.params.{$field}.tooltip")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="addons/abt__unitheme2/views/banners/components/colorpicker.tpl"}
</div>
</div>
{** content_bg **}
<script language="javascript">
function fn_change_content_bg(e) {
var $ = Tygh.$;
var position = $('.content_bg_position');
var align = $('.content_bg_align');
var opacity = $('.content_bg_opacity');
var color = $('.content_bg_color');
switch (e.val()) {
case 'none':
position.addClass('hidden');
align.addClass('hidden');
opacity.addClass('hidden');
color.addClass('hidden');
break;
case 'transparent':
position.removeClass('hidden');
align.addClass('hidden');
opacity.removeClass('hidden');
color.removeClass('hidden');
break;
case 'transparent_blur':
position.removeClass('hidden');
align.addClass('hidden');
opacity.removeClass('hidden');
color.removeClass('hidden');
break;
case 'transparent_gradient':
position.removeClass('hidden');
align.removeClass('hidden');
opacity.removeClass('hidden');
color.removeClass('hidden');
break;
case 'colored':
position.removeClass('hidden');
align.addClass('hidden');
opacity.addClass('hidden');
color.removeClass('hidden');
break;
}
}
</script>
<hr>
{$field="class"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div class="controls cm-trim"><div id="overlay_{$elm}" class="abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}"></div>
<input type="text" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="{$banner.$elm}" size="25" class="span10"/>
<p class="muted description">
<span style="opacity: 0.7">{__("abt__banner_extra_functional")}</span><br>
{foreach [
__("abt__ut2.banner.button_position_bottom") => 'b--button-position-bottom',
__("abt__ut2.banner.image_oversize") => 'b--image-zoom',
__("abt__ut2.banner.image_fit") => 'b--image-fit',
__("abt__ut2.banner.image_align_to_edge") => 'b--image-align-to-edge',
__("abt__ut2.banner.background_fading") => 'b--background-fading',
__("abt__ut2.banner.mask_margin") => 'b--mask-margin',
__("abt__ut2.banner.column") => 'b--in-columnar',
__("abt__ut2.banner.column_reverse") => 'b--column-reverse'
] as $classes}
<input type="checkbox"
id="{$name}_{$classes@key}"
class="cm-text-toggle btn-group-checkbox__checkbox"
{if $banner.$elm && $banner.$elm|strpos:$classes !== false}checked="checked"{/if}
data-ca-toggle-text="{$classes}"
data-ca-toggle-text-target-elem-id="elm_banner_{$elm}"
/>
<label class="btn btn-group-checkbox__label" for="{$name}_{$classes@key}">
{$classes@key}
</label>
{/foreach}
</p>
</div>
</div>
<hr>
{** data type / URL **}
{$field="data_type"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group hidden">
<label for="elm_banner_{$elm}" class="control-label{if $disabled || $banner["`$elm`_use_own"] == 'N'} disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}" onchange="Tygh.$('div[id*=data_type_]').addClass('hidden');Tygh.$('div[id*=data_type_' + this.value + ']').removeClass('hidden');">
{foreach ['url'] as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
<div id="data_type_url" class="{if $banner.abt__ut2_data_type|default:'url' != 'url'}hidden{/if}">
{$field="url"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label cm-trim {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="text" name="banner_data[{$elm}]" id="elm_banner_{$elm}" value="{$banner.$elm}" size="25" class="span10"/>
</div>
</div>
</div>
{$field="how_to_open"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{$how_to_opens=['in_this_window', 'in_new_window']}
<select name="banner_data[{$elm}]" id="elm_banner_{$elm}">
{foreach $how_to_opens as $e}
<option value="{$e}" {if $banner.$elm == $e}selected="selected"{/if}>{__("abt__ut2.banner.params.{$field}.variants.{$e}")}</option>
{/foreach}
</select>
</div>
</div>
{** Set banner activity period **}
<script language="javascript">
function fn_activate_calendar(el) {
Tygh.$('#elm_banner_abt__ut2_avail_from').prop('disabled', !el.checked);
Tygh.$('#elm_banner_abt__ut2_avail_till').prop('disabled', !el.checked);
if (!el.checked){
Tygh.$('#period_abt__ut2_avail_from,#period_abt__ut2_avail_till').addClass('hidden');
}else{
Tygh.$('#period_abt__ut2_avail_from,#period_abt__ut2_avail_till').removeClass('hidden');
}
}
</script>
{$field="use_avail_period"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
<input type="hidden" name="banner_data[{$elm}]" value="N"/>
<input type="checkbox" name="banner_data[{$elm}]" id="elm_banner_{$elm}" {if $banner.$elm == "YesNo::YES"|enum}checked="checked"{/if} value="Y" onclick="fn_activate_calendar(this);"/>
</div>
</div>
{capture name="calendar_disable"}{if $banner.$elm != "YesNo::YES"|enum}disabled="disabled"{/if}{/capture}
{$field="avail_from"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group {if $banner.abt__ut2_use_avail_period == 'N'}hidden{/if}" id="period_{$elm}">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="common/calendar.tpl" date_id="elm_banner_{$elm}" date_name="banner_data[{$elm}]" date_val=$banner.$elm start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
</div>
</div>
{$field="avail_till"}{$elm="abt__ut2`$device_prefix`_`$field`"}{$disabled=$field|fn_abt__ut2_is_disabled_field:$enabled_fields}
<div class="control-group {if $banner.abt__ut2_use_avail_period == 'N'}hidden{/if}" id="period_{$elm}">
<label for="elm_banner_{$elm}" class="control-label {if $disabled || $banner["`$elm`_use_own"] == 'N'}disabled{/if}">{__("abt__ut2.banner.params.{$field}")}</label>
{include file="addons/abt__unitheme2/views/banners/components/use_own.tpl"}
<div id="overlay_{$elm}" class="controls abt-ut2-overlay{if $disabled || $banner["`$elm`_use_own"] == 'N'} active{/if}">
{include file="common/calendar.tpl" date_id="elm_banner_{$elm}" date_name="banner_data[{$elm}]" date_val=$banner.$elm start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
</div>
</div>
