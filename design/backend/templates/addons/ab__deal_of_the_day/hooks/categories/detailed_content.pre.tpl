{if 'ab__deal_of_the_day.view'|fn_check_view_permissions}
{include file="common/subheader.tpl" title=__("ab__deal_of_the_day") target="#ab__dotd"}
<div id="ab__dotd" {if !'ab__deal_of_the_day.manage'|fn_check_view_permissions}class="cm-hide-inputs"{/if}>
<div class="control-group">
<label class="control-label">{__("ab__dotd.category_filter_icon")}:</label>
<div class="controls">
{include file="common/attach_images.tpl"
image_name="ab__dotd_cat_filter"
image_object_type="ab__dotd_cat_filter"
image_key=$id
hide_titles=true
no_detailed=true
hide_alt=true
image_pair=$category_data.ab__dotd_cat_filter}
</div>
</div>
</div>
{/if}