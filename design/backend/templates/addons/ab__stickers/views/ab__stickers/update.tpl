{** Use this variables to check sticker style **}
{$text_style = "Tygh\Enum\Addons\Ab_stickers\StickerStyles::TEXT"|constant}
{$graphic_style = "Tygh\Enum\Addons\Ab_stickers\StickerStyles::GRAPHIC"|constant}
{$pictogram_style = "Tygh\Enum\Addons\Ab_stickers\StickerStyles::PICTOGRAM"|constant}
{** Use this variables to check sticker type **}
{$dynamic_type = "Tygh\Enum\Addons\Ab_stickers\StickerTypes::DYNAMIC"|constant}
{$constant_type = "Tygh\Enum\Addons\Ab_stickers\StickerTypes::CONSTANT"|constant}
{$default_type = $smarty.request.type|default:$constant_type}
{$sticker_type = $sticker_data.type|default:$default_type}
{$sticker_style = $sticker_data.style|default:$text_style}
{strip}
{if $sticker_data}
{assign var="id" value = $sticker_data.sticker_id}
{else}
{assign var="id" value = 0}
{/if}
{$hide_inputs = !"ab__stickers.edit"|fn_check_view_permissions}
{capture name="mainbox"}
<form action="{""|fn_url}" method="post" class="form-horizontal form-edit{if $hide_inputs} cm-hide-inputs{/if}" name="ab__stickers_sticker_data_form" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="sticker_id" value="{$id}" />
<input type="hidden" name="sticker_data[type]" value="{$sticker_type}" />
<input type="hidden" name="selected_section" id="selected_section" value="{$selected_section}" />
{capture name="tabsbox"}
{hook name="ab__stickers:item_tabsbox_content"}
<div class="hidden" id="content_general">
{hook name="ab__stickers:item_general_content"}
<div class="control-group">
<label class="control-label cm-required" for="ab__stickers_name_for_admin">{__("name")}:</label>
<div class="controls">
<input type="text" name="sticker_data[name_for_admin]" id="ab__stickers_name_for_admin" value="{$sticker_data.name_for_admin}" size="25" class="input-large" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_style">{__("ab__stickers.style")}:</label>
<div class="controls">
<select name="sticker_data[style]" id="ab__stickers_style">
{foreach fn_ab__stickers_get_enum_list() as $style}
{if (($style === $pictogram_style && $sticker_type === $constant_type) || ($style !== $pictogram_style))}
<option value="{$style}"{if $sticker_style === $style} selected{/if}>{__("ab__stickers.style.$style")}</option>
{/if}
{/foreach}
</select>
</div>
</div>
<div class="sticker-type-settings{if $sticker_style !== $text_style} hidden{/if}" id="{$text_style}_sticker_settings">
<div class="control-group">
<label class="control-label" for="ab__stickers_name_for_desktop">{__("ab__stickers.name_for_desktop")}:</label>
<div class="controls">
<input type="text" name="sticker_data[name_for_desktop]" id="ab__stickers_name_for_desktop" value="{$sticker_data.name_for_desktop}" size="25" class="input-large" />
<p class="muted description">{__('ab__stickers.name_for_desktop.tooltip')}</p>
<p class="description">{__("ab__stickers.field_with_available_placeholders")}</p>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_name_for_mobile">{__("ab__stickers.name_for_mobile")}:</label>
<div class="controls">
<input type="text" name="sticker_data[name_for_mobile]" id="ab__stickers_name_for_mobile" value="{$sticker_data.name_for_mobile}" size="25" class="input-large" />
<p class="muted description">{__('ab__stickers.name_for_mobile.tooltip')}</p>
<p class="description">{__("ab__stickers.field_with_available_placeholders")}</p>
</div>
</div>
<div class="control-group">
{$use_theme_presets_href = "customization.update_mode?type=theme_editor&status=enable&s_layout=`$runtime.layout.layout_id`&s_storefront=`$app['storefront']->storefront_id`"|fn_url}
<label class="control-label" for="ab__stickers_use_theme_presets">{__("ab__stickers.use_theme_presets")}:</label>
<div class="controls">
<input type="hidden" name="sticker_data[appearance][use_theme_presets]" value="N">
<input id="ab__stickers_use_theme_presets" type="checkbox" name="sticker_data[appearance][use_theme_presets]" value="Y"{if $sticker_data.appearance.use_theme_presets == "Y"} checked{/if}>
<p class="muted description">{__("ab__stickers.use_theme_presets.tooltip", ["[href]" => $use_theme_presets_href])}</p>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_text_color">{__("ab__stickers.text_color")}:</label>
<div class="controls">
<div class="input-prepend">
{include file="views/theme_editor/components/colorpicker.tpl" cp_name="sticker_data[appearance][text_color]" cp_id="ab__stickers_text_color" cp_value=$sticker_data.appearance.text_color|default:'#FFFFFF'}
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_sticker_bg">{__("ab__stickers.sticker_bg")}:</label>
<div class="controls">
<div class="input-prepend">
{include file="views/theme_editor/components/colorpicker.tpl" cp_name="sticker_data[appearance][sticker_bg]" cp_id="ab__stickers_sticker_bg" cp_value=$sticker_data.appearance.sticker_bg|default:'#000000'}
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_sticker_bg_opacity">{__("ab__stickers.sticker_bg.opacity")}:</label>
<div class="controls">
<select name="sticker_data[appearance][sticker_bg_opacity]" id="ab__stickers_sticker_bg_opacity">
{foreach ["100%", "90%", "80%", "70%", "60%", "50%", "40%", "30%", "20%", "10%"] as $value}
<option value="{$value}"{if $sticker_data.appearance.sticker_bg_opacity == $value} selected{/if}>{$value}</option>
{/foreach}
</select>
</div>
</div>
{if $sticker_data.appearance.appearance_style|default:$addons.ab__stickers.ts_appearance != "beveled_angle"}
<div class="control-group">
<label class="control-label" for="ab__stickers_border_width">{__("ab__stickers.border_width")}:</label>
<div class="controls">
<select name="sticker_data[appearance][border_width]" id="ab__stickers_border_width">
{foreach [
"0" => __("do_not_use"),
"1px" => "1px",
"2px" => "2px",
"3px" => "3px"
] as $bd_w}
<option{if $sticker_data.appearance.border_width == $bd_w@key} selected{/if} value="{$bd_w@key}">{$bd_w}</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_border_color">{__("ab__stickers.border_color")}:</label>
<div class="controls">
<div class="input-prepend">
{include file="views/theme_editor/components/colorpicker.tpl" cp_name="sticker_data[appearance][border_color]" cp_id="ab__stickers_border_color" cp_value=$sticker_data.appearance.border_color|default:'#000000'}
</div>
</div>
</div>
{/if}
<div class="control-group">
<label class="control-label" for="ab__stickers_uppercase_text">{__("ab__stickers.uppercase_text")}:</label>
<div class="controls">
<input type="hidden" name="sticker_data[appearance][uppercase_text]" value="N">
<input id="ab__stickers_uppercase_text" type="checkbox" name="sticker_data[appearance][uppercase_text]" value="Y"{if $sticker_data.appearance.uppercase_text == "Y"} checked{/if}>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_appearance_style">{__("ab__stickers.appearance_style")}:</label>
<div class="controls">
<select name="sticker_data[appearance][appearance_style]" id="ab__stickers_appearance_style">
<option value="" title="{__("ab__stickers.appearance_style.default.tooltip")}">{__("default")} ({__("ab__stickers.appearance_style.`$addons.ab__stickers.ts_appearance`")})</option>
{foreach fn_ab__stickers_sticker_get_ts_appearance_styles() as $appearance_style}
<option value="{$appearance_style@key}"{if $appearance_style@key == $sticker_data.appearance.appearance_style} selected{/if}>
{$appearance_style}
</option>
{/foreach}
</select>
</div>
</div>
</div>
<div class="sticker-type-settings{if in_array($sticker_style, [$graphic_style, $pictogram_style])} hidden{/if}" id="{$graphic_style}_sticker_settings">
<div class="control-group">
<label class="control-label" for="ab__stickers_img">{__("image")}:</label>
<div class="controls">
{include file="common/attach_images.tpl" image_name="ab__sticker_img" image_object_type="ab__stickers" hide_titles=true image_pair=$sticker_data.main_pair no_detailed=true image_object_id=$id}
</div>
</div>
<div class="sticker-type-additional{if ($sticker_style !== $graphic_style)} hidden{/if}">
<div class="control-group">
<label class="control-label" for="ab__stickers_full_size_image_size">{__("ab__stickers.full_size.image_size")}:</label>
<div class="controls">
<select name="sticker_data[appearance][full_size_image_size]" id="ab__stickers_full_size_image_size">
{foreach ["16", "24", "32", "48", "64", "96", "128"] as $image_size}
<option value="{$image_size}"{if $sticker_data.appearance.full_size_image_size == $image_size} selected{/if}>{$image_size}x{$image_size}px</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_small_size_image_size">{__("ab__stickers.small_size.image_size")}:</label>
<div class="controls">
<select name="sticker_data[appearance][small_size_image_size]" id="ab__stickers_small_size_image_size">
{foreach ["16", "24", "32", "48", "64", "96", "128"] as $image_size}
<option value="{$image_size}"{if $sticker_data.appearance.small_size_image_size == $image_size} selected{/if}>{$image_size}x{$image_size}px</option>
{/foreach}
</select>
</div>
</div>
</div>
</div>
<div class="sticker-type-settings{if $sticker_style !== $pictogram_style} hidden{/if}" id="{$pictogram_style}_sticker_settings">
<div class="control-group">
<label class="control-label" for="ab__stickers_full_size_image_size_P">{__("ab__stickers.full_size.image_size")}:</label>
<div class="controls">
<select name="sticker_data[appearance][full_size_image_size]" id="ab__stickers_full_size_image_size_P">
{foreach ["48", "64"] as $image_size}
<option value="{$image_size}"{if $sticker_data.appearance.full_size_image_size == $image_size} selected{/if}>{$image_size}x{$image_size}px</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_small_size_image_size_P">{__("ab__stickers.small_size.image_size")}:</label>
<div class="controls">
<select name="sticker_data[appearance][small_size_image_size]" id="ab__stickers_small_size_image_size_P">
{foreach ["48", "64"] as $image_size}
<option value="{$image_size}"{if $sticker_data.appearance.small_size_image_size == $image_size} selected{/if}>{$image_size}x{$image_size}px</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_sticker_bg_P">{__("ab__stickers.sticker_bg")}:</label>
<div class="controls">
<div class="input-prepend">
<input type="text" name="sticker_data[appearance][sticker_bg]" id="ab__stickers_sticker_bg_P" value="{$sticker_data.appearance.sticker_bg|default:'rgba(255,255,255,0)'}" data-ca-spectrum-show-alpha="true" data-ca-spectrum-preferred-format="rgb" {if $cp_storage}data-ca-storage="{$cp_storage}"{/if} class="cm-colorpicker {if $cp_class}{$cp_class}{/if}" />
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_appearance_style_P">{__("ab__stickers.appearance_style")}:</label>
<div class="controls">
<select name="sticker_data[appearance][appearance_style]" id="ab__stickers_appearance_style_P">
<option value="" title="{__("ab__stickers.appearance_style.default.tooltip")}">{__("default")} ({__("ab__stickers.appearance_style.`$addons.ab__stickers.p_appearance`")})</option>
{foreach fn_ab__stickers_sticker_get_ts_appearance_styles() as $appearance_style}
<option value="{$appearance_style@key}"{if $appearance_style@key == $sticker_data.appearance.appearance_style} selected{/if}>
{$appearance_style}
</option>
{/foreach}
</select>
</div>
</div>
{if $id}
{include file="common/subheader.tpl" title=__("text") target="#pictogram-texts-list"}
<div class="in collapse" id="pictogram-texts-list">
<div class="control-group">
<label class="control-label">{__("preview")}:</label>
<div class="controls">
<div class="preview">
{include file="addons/ab__stickers/views/ab__stickers/components/sticker_preview.tpl" for_preview=true sticker=$sticker_data}
</div>
<div class="texts">
{include file="addons/ab__stickers/views/ab__stickers/components/pictogram_texts_list.tpl" texts=$sticker_data.pictogram_data}
</div>
</div>
</div>
<!--pictogram-texts-list--></div>
{/if}
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_sticker_description">{__("ab__stickers.description")}:</label>
<div class="controls">
<textarea name="sticker_data[description]" id="ab__stickers_sticker_description" cols="55" rows="8" class="input-large cm-wysiwyg">{$sticker_data.description}</textarea>
<p class="muted description">{__("ab__stickers.description.tooltip")}</p>
<p class="description">{__("ab__stickers.field_with_available_placeholders.wysiwyg")}</p>
</div>
</div>
<hr>
<div id="stickers_availability_setting" class="in collapse">
<fieldset>
{if !"ULTIMATE:FREE"|fn_allowed_for}
<div class="control-group">
<label class="control-label">{__("usergroups")}:</label>
<div class="controls">
{include file="common/select_usergroups.tpl" id="ug_id" name="sticker_data[usergroup_ids]" usergroups=["type" => "UsergroupTypes::TYPE_CUSTOMER"|enum, "status" => ["ObjectStatuses::ACTIVE"|enum, "ObjectStatuses::HIDDEN"|enum]]|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$sticker_data.usergroup_ids list_mode=false}
</div>
</div>
{/if}
<div class="control-group">
<label class="control-label" for="ab__stickers_position">{__("position_short")}:</label>
<div class="controls">
<input type="text" name="sticker_data[position]" id="ab__stickers_position" value="{$sticker_data.position|default:"0"}" size="3"/>
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_use_avail_period">{__("use_avail_period")}:</label>
<div class="controls">
<input type="checkbox" name="avail_period" id="elm_use_avail_period"{if $sticker_data.from_date || $sticker_data.to_date} checked="checked"{/if} value="Y" onclick="fn_activate_calendar(this);"/>
{literal}
<script language="javascript">
function fn_activate_calendar(el)
{
Tygh.$('#elm_date_holder_from').prop('disabled', !el.checked);
Tygh.$('#elm_date_holder_to').prop('disabled', !el.checked);
}
fn_activate_calendar(document.getElementById('elm_use_avail_period'));
</script>
{/literal}
</div>
</div>
{capture name="calendar_disable"}{if !$sticker_data.from_date && !$sticker_data.to_date}disabled="disabled"{/if}{/capture}
<div class="control-group">
<label class="control-label" for="elm_date_holder_from">{__("ab__stickers.avail_from")}:</label>
<div class="controls">
<input type="hidden" name="sticker_data[from_date]" value="0" />
{include file="common/calendar.tpl" date_id="elm_date_holder_from" date_name="sticker_data[from_date]" date_val=$sticker_data.from_date start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_date_holder_to">{__("ab__stickers.avail_till")}:</label>
<div class="controls">
<input type="hidden" name="sticker_data[to_date]" value="0" />
{include file="common/calendar.tpl" date_id="elm_date_holder_to" date_name="sticker_data[to_date]" date_val=$sticker_data.to_date start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
</div>
</div>
{if "MULTIVENDOR"|fn_allowed_for && $addons.ab__stickers.enable_for_vendors == "YesNo::YES"|enum}
<div class="control-group">
<label class="control-label" for="ab__stickers_vendor_edit">{__("ab__stickers.vendor_edit")}:</label>
<div class="controls">
<input type="hidden" name="sticker_data[vendor_edit]" value="N" />
<input type="checkbox" name="sticker_data[vendor_edit]" id="ab__stickers_vendor_edit" value="Y"{if $sticker_data.vendor_edit == "YesNo::YES"|enum} checked="checked"{/if} />
</div>
</div>
{/if}
{include file="common/select_status.tpl" input_name="sticker_data[status]" id="ab__stickers_status" obj_id=$id obj=$sticker_data hidden=false}
</fieldset>
</div>
{/hook}
<!--content_general--></div>
<div class="hidden" id="content_conditions">
{hook name="ab__stickers:item_conditions_content"}
<div class="conditions-tree">
{include file="addons/ab__stickers/views/ab__stickers/components/group.tpl" group=$sticker_data.conditions prefix="sticker_data[conditions]" root=true}
{script src="js/tygh/backend/promotion_update.js"}
{script src="js/tygh/node_cloning.js"}
</div>
{/hook}
<!--content_conditions--></div>
{if $sticker_type == $constant_type}
<div class="hidden" id="content_attached_products">
{hook name="ab__stickers:item_attached_products_content"}
<p class="muted" style="margin-bottom: 15px;">{__('ab__stickers.attached_stickers.sticker_page.tooltip')}</p>
<input name="current_attached_products" type="hidden" value="{$sticker_data.product_ids}">
{include file="pickers/products/picker.tpl" placement="right" input_name="attached_products" data_id="attached_products" item_ids=$sticker_data.product_ids type="links"}
{/hook}
<!--content_attached_products--></div>
{/if}
<div class="hidden" id="content_display">
{hook name="ab__stickers:item_display_on_content"}
{include file="addons/ab__stickers/views/ab__stickers/components/display_tab.tpl"}
{/hook}
<!--content_display--></div>
{if fn_allowed_for('MULTIVENDOR:ULTIMATE,ULTIMATE')}
<div class="hidden" id="content_storefronts">
{hook name="ab__stickers:item_storefronts_content"}
<p class="muted" style="margin-bottom: 15px">{__('ab__stickers.storefronts.sticker_page.description')}</p>
{include file="pickers/storefronts/picker.tpl"
multiple=true
input_name="sticker_data[storefront_ids]"
item_ids=$sticker_data.storefront_ids
data_id="storefront_ids"
use_keys="N"
view_only=$hide_inputs
but_meta="pull-right"
}
{/hook}
<!--content_storefronts--></div>
{else}
<input name="sticker_data[storefront_ids]" type="hidden" value="{$sticker_data.storefront_ids|default:$app['storefront']->storefront_id}">
{/if}
{/hook}
{/capture}
{include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}
{capture name="buttons"}
{if $id}
{capture name="tools_list"}
<li>{btn type="list" text=__("clone") href="ab__stickers.clone?sticker_ids[]=`$id`" method="POST"}</li>
<li>{btn type="list" text=__("delete") class="cm-confirm" href="ab__stickers.delete?sticker_ids[]=`$id`" method="POST"}</li>
{if $id && ($sticker_type == $constant_type || $sticker_style === $pictogram_style)}
<li class="divider"></li>
{if $sticker_type == $constant_type}
<li>{btn type="list" text=__("ab__stickers.generate.generated_ids.description", [1]) class="cm-post cm-comet cm-ajax" href="ab__stickers.cron.generate.`$id`?storefront_ids=`$app['storefront']->storefront_id`" method="GET"}</li>
{/if}
{if $sticker_style === $pictogram_style}
<li>{btn type="list" text=__("ab__stickers.pictogram.generate_for_products") class="cm-post cm-comet cm-ajax" href="ab__stickers.cron.generate_pictograms.`$id`?storefront_ids=`$app['storefront']->storefront_id`" method="GET"}</li>
{/if}
{/if}
{/capture}
{dropdown content=$smarty.capture.tools_list}
{/if}
{include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="ab__stickers_sticker_data_form" but_name="dispatch[ab__stickers.update]" save=$id}
{/capture}
</form>
{/capture}
{capture name="sidebar"}
{hook name="ab__stickers:update_sidebar"}
{include file="addons/ab__stickers/views/ab__stickers/components/update_page_notes.tpl"}
{/hook}
{/capture}
{if !$id}
{$title_end = __("ab__stickers.new_sticker")}
{else}
{$title_end = $sticker_data.name_for_admin}
{/if}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__stickers"}
{include file="common/mainbox.tpl"
title_start=__("ab__stickers")|truncate:40
title_end=$title_end
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
sidebar=$smarty.capture.sidebar
adv_buttons=$smarty.capture.adv_buttons
show_all_storefront=false
select_languages=true}
{/strip}