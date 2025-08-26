<li>
<form method="post" action="{""|fn_url}">
<select name="group_id" id="m_group_group_id">
<option value="0">--</option>
{foreach fn_abt__ut2_get_banner_groups_list() as $group_id => $group_name}
<option value="{$group_id}">{$group_name}</option>
{/foreach}
</select>
<input type="hidden" name="redirect_url" value="{$config.current_url}">
<div class="">
{include file="buttons/button.tpl"
but_name="dispatch[banners.m_group]"
but_text=__("update")
but_role="submit"
}
</div>
</form>
</li>
