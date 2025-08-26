<p class="muted" style="margin-bottom: 15px;">{__("ab__stickers.output_settings.tooltip")}</p>
{$display_places = fn_get_schema('ab__stickers', 'display_places')}
{$not_display_place = 'Addons\Ab_stickers\StickerPlaces::NOT_DISPLAY'|enum}
{$product_templates = fn_ab__stickers_get_templates_displays()}
{foreach from=$product_templates item='product_template' name='product_templates'}
{$product_template_name=$product_template.old_id}
{$lang_var = "ab__stickers.display_on.`$product_template.id`"}
{if fn_is_lang_var_exists($lang_var)}
{$product_template_name = __($lang_var)}
{elseif fn_is_lang_var_exists($product_template.old_id)}
{$product_template_name = __($product_template.old_id)}
{/if}
{$template_display_place = $sticker_data['display_place'][$product_template.tpl]|default:$not_display_place}
{if in_array($template_display_place, $product_template.prohibited_display_places|default:[])}
{$template_display_place = $not_display_place}
{/if}
{$section_class = ''}
{if $sticker_style === $pictogram_style && !$product_template.pictograms_allowed}
{$section_class = ' hidden'}
{/if}
<div class="ab__sticker_display_section{$section_class}" id="ab__sticker_display_section_{$product_template.id}" data-ab-pictograms-allowed="{$product_template.pictograms_allowed}">
{include file="common/subheader.tpl" title=$product_template_name target="#sticker_display_{$product_template.id}"}
<div class="in collapse" id="sticker_display_{$product_template.id}">
<div class="control-group ab__sticker_control_group-display_place{if $sticker_style === $pictogram_style} hidden{/if}" data-ab-pictogram-toogle="hide">
{$display_place_id = "display_place_`$product_template.id`"}
<label class="control-label" for="ab__stickers_{$display_place_id}">{__('ab__stickers.display_place')}:</label>
<div class="controls">
<select name="sticker_data[display_place][{$product_template.tpl}]" id="ab__stickers_{$display_place_id}">
<option value="{$not_display_place}"{if $template_display_place === $not_display_place} selected="selected"{/if}>
{__('ab__stickers.display_place.not_display')}
</option>
{foreach $display_places as $display_place}
{$is_prohibited = in_array($display_place.id, $product_template.prohibited_display_places|default:[])}
<option value="{$display_place.id}"{if $template_display_place == $display_place.id} selected{/if}{if $is_prohibited} disabled{/if}>
{__("ab__stickers.display_place.`$display_place.id`")}
</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group ab__sticker_control_group-display_pictogram{if $sticker_style !== $pictogram_style} hidden{/if}" data-ab-pictogram-toogle="show">
<label class="control-label" for="ab__stickers_display_pictogram_{$display_place_id}">{__('ab__stickers.display_pictogram')}:</label>
<div class="controls">
<input type="checkbox" id="ab__stickers_display_pictogram_{$display_place_id}"{if $template_display_place !== $not_display_place} checked{/if}>
</div>
</div>
<div class="control-group ab__sticker_control_group-output_position{if $sticker_style === $pictogram_style || $template_display_place !== 'Addons\Ab_stickers\StickerPlaces::PRODUCT_IMAGE'|enum} hidden{/if}" data-ab-pictogram-toogle="hide">
<label class="control-label" for="ab__stickers_output_position_{$product_template.id}">{__("ab__stickers.output_position")}:</label>
<div class="controls">
<select name="sticker_data[output_position][{$product_template.tpl}]" id="ab__stickers_output_position_{$product_template.id}">
{foreach fn_ab__stickers_get_enum_list('OutputPositions') as $output_position}
{$is_prohibited = in_array($output_position, $product_template.prohibited_list_positions|default:[])}
<option value="{$output_position}"
{if $sticker_data.output_position[$product_template.tpl] == $output_position && !$is_prohibited} selected{/if}
{if $is_prohibited} disabled{/if}
>
{__("ab__stickers.output_position.`$output_position`")}
</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
{$val = $sticker_data['display_on'][$product_template.tpl]|default:$product_template.display_size|default:$product_template.default_value}
<label class="control-label" for="ab__stickers_display_on_{$product_template.id}">{__('ab__stickers.display_size')}:</label>
<div class="controls">
<select name="sticker_data[display_on][{$product_template.tpl}]" id="ab__stickers_display_on_{$product_template.id}" style="vertical-align:top">
{foreach fn_ab__stickers_get_enum_list('StickerSizes') as $display_size}
{$is_prohibited = in_array($display_size, $product_template.prohibited_display_size|default:[])}
<option value="{$display_size}"{if $is_prohibited} disabled{/if}{if $val == $display_size && !$is_prohibited} selected{/if}>
{__("ab__stickers.output_settings.{$display_size}")}
</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_user_class_{$product_template.id}">{__("ab__stickers.params.class")}:</label>
<div class="controls">
<input id="ab__stickers_user_class_{$product_template.id}" type="text" name="sticker_data[user_class][{$product_template.tpl}]" value="{$sticker_data.user_class[$product_template.tpl]|default:""}">
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_display_on_toggler_{$product_template.id}">{__("block_manager.availability.show_on")}:</label>
{include file="addons/ab__stickers/views/ab__stickers/components/display_on_toggler.tpl" place=$product_template.id name="ab__stickers_display_on_toggler_{$product_template.id}" tpl=$product_template.tpl}
</div>
</div>
</div>
{if !$smarty.foreach.product_templates.last}
<hr>
{/if}
{/foreach}