{* colorpicker insertion *}
{$elm1="abt__ut2`$device_prefix`_`$field`_use"}
<input type="hidden" name="banner_data[{$elm1}]" value="N" />
<input type="checkbox" name="banner_data[{$elm1}]" id="elm_banner_{$elm1}" value="Y" {if $banner.$elm1 == "YesNo::YES"|enum}checked="checked"{/if} style="margin-right: 5px;"/>
{include file="views/theme_editor/components/colorpicker.tpl" cp_name="banner_data[{$elm}]" cp_id="elm_banner_{$elm}" cp_value=$banner.$elm}
&nbsp;&nbsp;&nbsp;<a href="https://www.canva.com/colors/color-palettes/" title="{__("theme_editor.color_gen_algorithm")}" target="_blank">{__("abt__ut2.banner.another_color")}</a>