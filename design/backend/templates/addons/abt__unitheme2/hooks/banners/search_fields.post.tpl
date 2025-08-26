<div class="sidebar-field">
<label for="elm_type">{__("abt__ut2.banner.banner_group")}</label>
<div class="controls">
<select name="group_id" id="elm_group_id">
<option value="">{__("all")}</option>
<option value="-1" {if $smarty.request.group_id == -1} selected="selected" {/if}>{__("abt__ut2.banner.filters.no_group")}</option>
{foreach fn_abt__ut2_get_banner_groups_list() as $group_id => $group_name}
<option value="{$group_id}" {if $smarty.request.group_id == $group_id} selected="selected" {/if}>{$group_name}</option>
{/foreach}
</select>
</div>
</div>