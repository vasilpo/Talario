{strip}
{if $motivation_item_data}
{assign var="id" value=$motivation_item_data.motivation_item_id}
{else}
{assign var="id" value=0}
{/if}
{$hide_inputs = !"ab__motivation_block.change"|fn_check_view_permissions}
{capture name="mainbox"}
<form action="{""|fn_url}" method="post" class="form-horizontal form-edit{if $hide_inputs} cm-hide-inputs{/if}" name="ab__mb_motivation_item_data_form" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="motivation_item_id" value="{$id}" />
<input type="hidden" name="storefront_id" value="{$app["storefront"]->storefront_id}" />
<input type="hidden" name="selected_section" id="selected_section" value="{$selected_section}" />
{capture name="tabsbox"}
<div id="content_general">
{hook name="ab__mb:item_general_content"}
<div class="control-group">
<label class="control-label cm-required" for="ab__mb_name">{__("name")}:</label>
<div class="controls">
<input type="text" name="motivation_item_data[name]" id="ab__mb_name" value="{$motivation_item_data.name}" size="25" class="input-large" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__mb_position">{__("position_short")}:</label>
<div class="controls">
<input type="text" name="motivation_item_data[position]" id="ab__mb_position" value="{$motivation_item_data.position|default:"0"}" size="3"/>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__mb_icon_type">{__("ab__mb.icon_type")}:</label>
<div class="controls">
<select name="motivation_item_data[icon_type]" id="ab__mb_icon_type" onchange="$('.ab__mb_container').addClass('hidden');$('#ab__mb_' + this.value + '_container').removeClass('hidden');">
<option value="nothing"{if $motivation_item_data.icon_type == 'nothing'} selected{/if}>{__("ab__mb_icon_type.nothing")}</option>
<option value="img"{if $motivation_item_data.icon_type == 'img'} selected{/if}>{__("ab__mb_icon_type.img")}</option>
<option value="icon"{if $motivation_item_data.icon_type == 'icon'} selected{/if}>{__("ab__mb_icon_type.icon")}</option>
</select>
</div>
</div>
<div id="ab__mb_nothing_container" class="ab__mb_container{if $motivation_item_data.icon_type != 'nothing'} hidden{/if}"></div>
<div id="ab__mb_img_container" class="ab__mb_container{if $motivation_item_data.icon_type != 'img'} hidden{/if}">
<div class="control-group">
<label class="control-label" for="ab__mb_img">{__("image")}:</label>
<div class="controls">
{include file="common/attach_images.tpl" image_name="ab__mb_img" image_object_type="motivation_item" image_pair=$motivation_item_data.main_pair no_thumbnail=true}
</div>
</div>
</div>
<div id="ab__mb_icon_container" class="ab__mb_container{if $motivation_item_data.icon_type != 'icon'} hidden{/if}">
<div class="control-group">
<label class="control-label" for="ab__mb_icon_class">{__("ab__mb.icon_class")}{include file="common/tooltip.tpl" tooltip=__('ab__mb_icon_class.tooltip', ['[link]' => 'ab__motivation_block.icons'|fn_url])}:</label>
<div class="controls">
<input type="text" name="motivation_item_data[icon_class]" id="ab__mb_icon_class" value="{$motivation_item_data.icon_class}" class="input-large" />
</div>
</div>
<div class="control-group">
<label class="control-label cm-color" for="ab__mb_icon_color">{__("ab__mb.icon_color")}:</label>
<div class="controls">
{include file="views/theme_editor/components/colorpicker.tpl" cp_name="motivation_item_data[icon_color]" cp_id="ab__mb_icon_color" cp_value=$motivation_item_data.icon_color|default:'#FFFFFF'}
</div>
</div>
</div>
<div class="control-group ab-mb-templates-select">
<label class="control-label" for="ab__mb_template_path">{__("template")}{include file="common/tooltip.tpl" tooltip=__('ab__mb.template_path.tooltip')}:</label>
<div class="controls">
<select name="motivation_item_data[template_path]" id="ab__mb_template_path" class="ab-mb-add-title-to-descr">
{hook name="ab__mb:templates_select"}
{function print_template}
<option value="{$template.template_path}"
{if $motivation_item_data.template_path == $template.template_path} selected{if $template.disabled}{'template'|fn_ab__mb_disabled_option_choosen}{/if}{/if}
{if $template.disabled} disabled{/if}
{if $template.tooltip} title="{$template.tooltip|strip_tags}"{/if}
{if $template.settings} data-settings="{$template.settings}"{/if}
>
{$template.template_name|strip_tags}
</option>
{/function}
{*<option value="" class="show-fields"{if $motivation_item_data.template_path == ''} selected{/if}>{__('ab__mb.template_path.variants.custom')}</option>*}
{foreach $ab__mb_templates as $name => $template}
{if $template.subitems}
<optgroup label="{__($name)}">
{foreach $template.subitems as $sub_template}
{print_template template=$sub_template}
{/foreach}
</optgroup>
{else}
{print_template template=$template}
{/if}
{/foreach}
{/hook}
</select>
{capture name="ab__mb_templates_settings"}{/capture}
{function print_template_settings}
<a href="#" onclick="javascript:void(0)" id="sw_mb_ts_{$template.settings}" class="cm-combination{if $motivation_item_data.template_path == $template.template_path} open{else}" style="display: none{/if}">&nbsp;{__('settings')}</a>
{capture name="ab__mb_templates_settings"}
{$smarty.capture.ab__mb_templates_settings nofilter}
<div id="mb_ts_{$template.settings}"{if $motivation_item_data.template_path != $template.template_path} class="hidden"{/if}>
<hr/>
{include file="addons/ab__motivation_block/views/ab__motivation_block/components/templates_settings/settings_`$template.settings`.tpl" tmpl_name=$template.template_path name_prefix="motivation_item_data[template_settings]" id_pref="tmpl_settings" t_settings=$motivation_item_data.template_settings}
<hr/>
</div>
{/capture}
{/function}
{foreach $ab__mb_templates as $template}
{if $template.subitems}
{foreach $template.subitems as $sub_template}
{if $sub_template.settings}
{print_template_settings template=$sub_template}
{/if}
{/foreach}
{elseif $template.settings}
{print_template_settings template=$template}
{/if}
{/foreach}
<p class="description" style="max-width: 70%; font-size: 13px; line-height: 1.3"></p>
</div>
{if !$motivation_item_data.template_path}
<script>
Tygh.$("#ab__mb_template_path option:first").attr('selected','selected');
Tygh.$("#ab__mb_template_path").change();
</script>
{/if}
</div>
{$smarty.capture.ab__mb_templates_settings nofilter}
{$theme_name = fn_get_theme_path('[theme]', "SiteArea::STOREFRONT"|enum, null, true, $motivation_item_data.storefront_id)}
{if in_array($theme_name, ['abt__unitheme2', 'abt__youpitheme'])}
{if $theme_name === 'abt__youpitheme'}
{$lang_prefix = 'abt__yt'}
{else}
{$lang_prefix = 'abt__ut2'}
{/if}
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive">
<thead>
<tr>
<th width="25%"></th>
<th width="25%">{__("`$lang_prefix`.settings.value.desktop")}</th>
<th width="25%">{__("`$lang_prefix`.settings.value.tablet")}</th>
<th width="25%">{__("`$lang_prefix`.settings.value.mobile")}</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<label class="control-label" for="ab__mb_expanded_desktop">{__("ab__mb_expanded")}{include file="common/tooltip.tpl" tooltip=__('ab__mb.only_for.vertical_tabs.tooltip')}:</label>
</td>
<td data-th="{__("`$lang_prefix`.settings.value.desktop")}">
<input type="hidden" value="N" name="motivation_item_data[expanded_desktop]">
<input id="ab__mb_expanded_desktop" type="checkbox" value="{"YesNo::YES"|enum}" name="motivation_item_data[expanded_desktop]"{if $motivation_item_data.expanded_desktop == "YesNo::YES"|enum} checked="checked"{/if}>
</td>
<td data-th="{__("`$lang_prefix`.settings.value.tablet")}">
<input type="hidden" value="N" name="motivation_item_data[expanded_tablet]">
<input id="ab__mb_expanded_tablet" type="checkbox" value="{"YesNo::YES"|enum}" name="motivation_item_data[expanded_tablet]"{if $motivation_item_data.expanded_tablet == "YesNo::YES"|enum} checked="checked"{/if}>
</td>
<td data-th="{__("`$lang_prefix`.settings.value.mobile")}">
<input type="hidden" value="N" name="motivation_item_data[expanded_mobile]">
<input id="ab__mb_expanded_mobile" type="checkbox" value="{"YesNo::YES"|enum}" name="motivation_item_data[expanded_mobile]"{if $motivation_item_data.expanded_mobile == "YesNo::YES"|enum} checked="checked"{/if}>
</td>
</tr>
</tbody>
</table>
</div>
{else}
<div class="control-group">
<label class="control-label" for="ab__mb_expanded_desktop">{__("ab__mb_expanded")}{include file="common/tooltip.tpl" tooltip=__('ab__mb.only_for.vertical_tabs.tooltip')}:</label>
<div class="controls">
<input type="hidden" name="motivation_item_data[expanded_desktop]" value="{"YesNo::NO"|enum}" />
<input type="checkbox" name="motivation_item_data[expanded_desktop]" id="ab__mb_expanded_desktop" value="{"YesNo::YES"|enum}"{if $motivation_item_data.expanded_desktop === "YesNo::YES"|enum} checked="checked"{/if} />
</div>
</div>
{/if}
{if !"ULTIMATE:FREE"|fn_allowed_for}
<div class="control-group">
<label class="control-label">{__("usergroups")}:</label>
<div class="controls">
{include file="common/select_usergroups.tpl" id="ug_id" name="motivation_item_data[usergroup_ids]" usergroups=["type"=>"C", "status"=>["A", "H"]]|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$motivation_item_data.usergroup_ids input_extra="" list_mode=false}
</div>
</div>
{/if}
{if "MULTIVENDOR"|fn_allowed_for}
<div class="control-group">
<label class="control-label" for="ab__mb_vendor_edit">{__("ab__mb_vendor_edit")}:</label>
<div class="controls">
<input type="hidden" name="motivation_item_data[vendor_edit]" value="{"YesNo::NO"|enum}" />
<input type="checkbox" name="motivation_item_data[vendor_edit]" id="ab__mb_vendor_edit" value="{"YesNo::YES"|enum}"{if $motivation_item_data.vendor_edit == "YesNo::YES"|enum} checked="checked"{/if} />
</div>
</div>
{/if}
{include file="common/select_status.tpl" input_name="motivation_item_data[status]" id="ab__mb_status" obj_id=$id obj=$motivation_item_data hidden=false}
{/hook}
<!--content_general--></div>
<div id="content_categories">
{if fn_check_view_permissions("categories.manage", "GET")}
{$type = "categories"}
{include file="addons/ab__motivation_block/views/ab__motivation_block/components/exclude_ids.tpl" type=$type}
{$no_items_text = __("text_all_categories_included")}
{if $motivation_item_data.categories_ids}
{$categories_ids = ","|explode:$motivation_item_data.categories_ids}
{/if}
{$company_ids = $app["storefront"]->getCompanyIds()}
{if !$company_ids}
{$company_ids = [0]}
{/if}
{include
file="pickers/categories/picker.tpl"
company_ids=implode(",", $company_ids)
multiple=true
input_name="motivation_item_data[categories_ids]"
item_ids=$categories_ids
data_id="category_ids_`$id`"
no_item_text=$no_items_text
use_keys="YesNo::NO"|enum
but_meta="pull-right"
}
{*owner_company_id=$motivation_item_data.company_id*}
{/if}
<!--content_categories--></div>
<div id="content_destinations">
{if fn_check_view_permissions("destinations.manage", "GET")}
{$type = "destinations"}
{include file="addons/ab__motivation_block/views/ab__motivation_block/components/exclude_ids.tpl" type=$type}
{$no_items_text = __("ab__mb.destinations.text_all_destinations_included")}
{if $motivation_item_data.destinations_ids}
{$destinations_ids = ","|explode:$motivation_item_data.destinations_ids}
{/if}
{include
file="addons/ab__motivation_block/pickers/destinations/picker.tpl"
multiple=true
input_name="motivation_item_data[destinations_ids]"
item_ids=$destinations_ids
data_id="destinations_ids_`$id`"
no_item_text=$no_items_text
but_meta="pull-right"
hide_delete_button=$hide_inputs
}
{/if}
<!--content_destinations--></div>
<div id="content_products">
{include file="addons/ab__motivation_block/views/ab__motivation_block/components/exclude_ids.tpl" type="products"}
{include file="views/products/components/picker/block_manager_picker.tpl"
multiple = true
input_name = "motivation_item_data[product_ids]"
show_positions = true
item_ids = $motivation_item_data.product_ids
no_item_text = __("ab__mb.destinations.text_all_destinations_included")
view_mode = "external"
}
<!--content_products--></div>
{/capture}
{include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}
{capture name="buttons"}
{include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="ab__mb_motivation_item_data_form" but_name="dispatch[ab__motivation_block.update]" save=$id}
{/capture}
</form>
{/capture}
{if !$id}
{$title_end = __("ab__mb.new_motivation_item")}
{else}
{$title_end = $motivation_item_data.name}
{/if}
{capture name="sidebar"}
{hook name="ab__mb:update_sidebar"}
{include file="addons/ab__motivation_block/views/ab__motivation_block/components/update_page_notes.tpl"}
{/hook}
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__motivation_block"}
{include file="common/mainbox.tpl"
title_start=__('ab__motivation_block')|truncate:40
title_end=$title_end
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
sidebar=$smarty.capture.sidebar
adv_buttons=$smarty.capture.adv_buttons
select_languages=true}
{/strip}