{if $category_banner_data}
{assign var="id" value=$category_banner_data.category_banner_id}
{else}
{assign var="id" value=0}
{/if}
{capture name="mainbox"}
<form action="{""|fn_url}" method="post" class="form-horizontal form-edit {if ""|fn_check_form_permissions} cm-hide-inputs{/if}" name="category_banner_form" id="category_banner_form" enctype="multipart/form-data">
<input type="hidden" class="cm-no-hide-input" name="category_banner_id" value="{$id}" />
{include file="common/subheader.tpl" title="{__('ab__cb.form.content_general')}" target="#content_general"}
<div id="content_general" class="in collapse">
<div class="control-group">
<label for="elm_category_banner" class="control-label cm-required">{__("name")}</label>
<div class="controls">
<input type="text" name="category_banner_data[category_banner]" id="elm_category_banner" value="{$category_banner_data.category_banner}" size="25" class="input-large" />
</div>
</div>
<div class="control-group" id="category_banner_categories">
{$rnd=rand()}
<label for="categories_{$rnd}_ids" class="control-label cm-required">{__("categories")}</label>
<div class="controls">
{include file="pickers/categories/picker.tpl" hide_input="Y" rnd=$rnd data_id="categories" input_name="category_banner_data[category_ids]" item_ids=$category_banner_data.category_ids hide_link=true hide_delete_button=true display_input_id="category_ids" disable_no_item_text=true view_mode="list" but_meta="btn" show_active_path=true}
</div>
<!--category_banner_categories--></div>
<div class="control-group">
<label for="elm_include_subcategories" class="control-label">{__("ab__cb.form.include_subcategories")}</label>
<div class="controls">
<input type="hidden" name="category_banner_data[include_subcategories]" value="N" />
<input type="checkbox" name="category_banner_data[include_subcategories]" id="elm_include_subcategories" value="Y" {if $category_banner_data.include_subcategories == "Y"}checked="checked"{/if} />
</div>
</div>
<div class="control-group">
<label class="control-label">{__("ab__cb.form.grid_image")}</label>
<div class="controls">
{include file="common/attach_images.tpl" image_name="category_banners_main" image_object_type="category_banner" image_pair=$category_banner_data.main_pair no_detailed=true hide_titles=true}
</div>
</div>
<div class="control-group">
<label class="control-label">{__("ab__cb.form.list_image")}</label>
<div class="controls">
{include file="common/attach_images.tpl" image_name="category_banners_list_image" image_object_type="category_banner" image_pair=$category_banner_data.list_pair no_detailed=true hide_titles=true image_type="L"}
</div>
</div>
<div class="control-group">
<label class="control-label">{__("ab__cb.form.short_list_image")}</label>
<div class="controls">
{include file="common/attach_images.tpl" image_name="category_banners_short_list_image" image_object_type="category_banner" image_pair=$category_banner_data.short_list_pair no_detailed=true hide_titles=true image_type="S"}
</div>
</div>
<div class="control-group">
<label for="elm_position" class="control-label">{__("ab__cb.form.position")}</label>
<div class="controls">
<input type="text" name="category_banner_data[position]" id="elm_position" value="{$category_banner_data.position}" size="25" class="input-large" />
</div>
</div>
{include file="common/select_status.tpl" input_name="category_banner_data[status]" id="elm_category_banner_status" obj_id=$id obj=$category_banner_data hidden=false}
</div>
{include file="common/subheader.tpl" title="{__('ab__cb.form.content_link')}" target="#content_link"}
<div id="content_link" class="in collapse">
<div class="control-group">
<label for="elm_url" class="control-label">{__("ab__cb.form.url")}</label>
<div class="controls">
<input type="text" name="category_banner_data[url]" id="elm_url" value="{$category_banner_data.url}" size="25" class="input-large" />
</div>
</div>
<div class="control-group">
<label for="elm_target_blank" class="control-label">{__("ab__cb.form.target_blank")}</label>
<div class="controls">
<input type="hidden" name="category_banner_data[target_blank]" value="N" />
<input type="checkbox" name="category_banner_data[target_blank]" id="elm_target_blank" value="Y" {if $category_banner_data.target_blank == "Y"}checked="checked"{/if} />
</div>
</div>
<div class="control-group">
<label for="elm_nofollow" class="control-label">{__("ab__cb.form.nofollow")}</label>
<div class="controls">
<input type="hidden" name="category_banner_data[nofollow]" value="N" />
<input type="checkbox" name="category_banner_data[nofollow]" id="elm_nofollow" value="Y" {if $category_banner_data.nofollow == "Y"}checked="checked"{/if} />
</div>
</div>
</div>
{include file="common/subheader.tpl" title="{__('ab__cb.form.content_schedule')}" target="#content_schedule"}
<div id="content_schedule" class="in collapse">
<div class="control-group">
<label class="control-label" for="elm_from_date_{$id}">{__("ab__cb.form.from_date")}</label>
<div class="controls">
{include file="common/calendar.tpl" date_id="elm_from_date_`$id`" date_name="category_banner_data[from_date]" date_val=$category_banner_data.from_date start_year=$settings.Company.company_start_year}
{include file="addons/ab__category_banners/views/ab__category_banners/components/time.tpl"
input_name="category_banner_data[from_time]"
id="from_time"
time=$category_banner_data.from_date
}
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_to_date_{$id}">{__("ab__cb.form.to_date")}</label>
<div class="controls">
{include file="common/calendar.tpl" date_id="elm_to_date_`$id`" date_name="category_banner_data[to_date]" date_val=$category_banner_data.to_date start_year=$settings.Company.company_start_year}
{include file="addons/ab__category_banners/views/ab__category_banners/components/time.tpl"
input_name="category_banner_data[to_time]"
id="to_time"
time=$category_banner_data.to_date
}
</div>
</div>
{$days =[1=>__("ab__cb.form.monday"),
2=>__("ab__cb.form.tuesday"),
3=>__("ab__cb.form.wednesday"),
4=>__("ab__cb.form.thursday"),
5=>__("ab__cb.form.friday"),
6=>__("ab__cb.form.saturday"),
7=>__("ab__cb.form.sunday")
]}
{foreach $days as $index => $day_name}
<div class="control-group"{if $category_banner_data.repeat[$index].active == "Y" || !$category_banner_data.repeat[$index].active} style="background-color:#f5f5f5"{/if}>
<label class="control-label" for="elm_repeat_{$index}">{$day_name}</label>
<div class="controls">
<input type="hidden" name="category_banner_data[repeat][{$index}][active]" value="N" />
<input class="ab__cb-form-checkbox" type="checkbox" name="category_banner_data[repeat][{$index}][active]" id="elm_repeat_{$index}" value="Y" {if $category_banner_data.repeat[$index].active == "Y" || !$category_banner_data.repeat[{$index}].active}checked="checked"{/if} />
{include file="addons/ab__category_banners/views/ab__category_banners/components/time.tpl"
input_name="category_banner_data[repeat][{$index}][time_from]"
time=$category_banner_data.repeat[{$index}].time_from
grinvich=true
no_failed_msg=true
}
-
{include file="addons/ab__category_banners/views/ab__category_banners/components/time.tpl"
input_name="category_banner_data[repeat][{$index}][time_to]"
time=$category_banner_data.repeat[{$index}].time_to
grinvich=true
}
</div>
</div>
{/foreach}
</div>
{capture name="buttons"}
{include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="category_banner_form" but_name="dispatch[ab__category_banners.update]" save=$id}
{/capture}
</form>
<script>
(function (_, $) {
$('input[id^=elm_repeat_]').change(function() {
if ($(this).is(":checked")) {
$(this).closest('.control-group').css('background-color', '#f5f5f5');
} else {
$(this).closest('.control-group').css('background-color', 'transparent');
}
});
})(Tygh, Tygh.$);
</script>
{/capture}
{if $id}
{$title_end = $category_banner_data.category_banner}
{else}
{$title_end = __("ab__category_banners.adding")}
{/if}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__category_banners"}
{include
file="common/mainbox.tpl"
title_start = __("ab__category_banners")|truncate:40
title_end = $title_end
content = $smarty.capture.mainbox
buttons = $smarty.capture.buttons
adv_buttons = $smarty.capture.adv_buttons
select_languages = true
}