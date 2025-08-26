{if $language_direction == "rtl"}
{$direction = "right"}
{else}
{$direction = "left"}
{/if}
{if "ULTIMATE"|fn_allowed_for}
{$storefront_id=$app.storefront->storefront_id}
{else}
{$storefront_id=$smarty.request.s_storefront|default:0}
{/if}
{$smart.request.group_id = 0}
{$has_permission = fn_check_permissions("banners", "update_status", "admin", "POST")}
{$rev=$smarty.request.content_id|default:"banners_table_tree"}
{$r_url=$current_dispatch}
{$c_url=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{$show_in_popup = true}
{capture name="mainbox"}
{$hide_inputs = ""|fn_check_form_permissions}
<form action="{""|fn_url}" method="post" name="banners_form" id="banners_form">
<div class="items-container" id="banners_table_tree">
{if $banners_tree}
{capture name="banners_table"}
<div class="table-responsive-wrapper longtap-selection">
<table class="table table-middle table--relative table-responsive">
<thead
data-ca-bulkedit-default-object="true"
data-ca-bulkedit-component="defaultObject"
>
<tr>
<th width="6%" class="left mobile-hide">
{include file="common/check_items.tpl" is_check_disabled=!$has_permission check_statuses=($has_permission) ? $banner_statuses : '' }
<input type="checkbox"
class="bulkedit-toggler hide"
data-ca-bulkedit-disable="[data-ca-bulkedit-default-object=true]"
data-ca-bulkedit-enable="[data-ca-bulkedit-expanded-object=true]"
/>
</th>
<th><a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__('group')}/{__("banner")}{if $search.sort_by === "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
<th width="10%" class="mobile-hide"><a class="cm-ajax" href="{"`$c_url`&sort_by=type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("type")}{if $search.sort_by === "type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
<th width="15%"><a class="cm-ajax" href="{"`$c_url`&sort_by=timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("creation_date")}{if $search.sort_by === "timestamp"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
{hook name="banners:manage_header"}
{/hook}
<th width="6%" class="mobile-hide">&nbsp;</th>
<th width="10%" class="right"><a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by === "status"}{$c_icon nofilter}{/if}</a></th>
</tr>
</thead>
<div class="table-wrapper">
{include file="addons/abt__unitheme2/views/banners/components/banners_tree.tpl"
header="1"
st_result_ids="banners_stats"
st_return_url=$config.current_url
direction=$direction
}
</div>
</table>
</div>
{/capture}
{include file="common/context_menu_wrapper.tpl"
form="banner_tree_form"
object="banners"
items=$smarty.capture.banners_table
has_permissions=$has_permission
}
{else}
<p class="no-items">{__("no_items")}</p>
{/if}
<!--banners_table_tree--></div>
{capture name="adv_buttons"}
{hook name="banners:adv_buttons"}
{include file="common/tools.tpl" tool_href="banners.add" prefix="top" hide_tools="true" title=__("add_banner") icon="icon-plus"}
{/hook}
{/capture}
{capture name="sidebar"}
{hook name="banners:manage_sidebar"}
{include file="common/saved_search.tpl" dispatch="banners.manage" view_type="banners"}
{include file="addons/banners/views/banners/components/banners_search_form.tpl" dispatch="banners.manage"}
{/hook}
{/capture}
</form>
{/capture}
{include file="common/mainbox.tpl"
title=__("banners")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
sidebar=$smarty.capture.sidebar
adv_buttons=$smarty.capture.adv_buttons
select_languages=true
select_storefront=true
selected_storefront_id=$storefront_id
}
