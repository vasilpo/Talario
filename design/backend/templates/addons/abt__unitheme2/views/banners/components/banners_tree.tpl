{if $banners_tree}
{if !empty($sw)}
<tbody class="{if !$expand_all}hidden{/if}" id="{$div_id}" {if $smarty.cookies.{"banner`$key`"}}style="display:table-row-group" {/if}>
{foreach from=$banners_tree key="key" item="banner"}
<tr class="cm-row-status-{$banner.status|lower} cm-longtap-target"
{if $has_permission}
data-ca-longtap-action="setCheckBox"
data-ca-longtap-target="input.cm-item"
data-ca-id="{$banner.banner_id}"
{/if}
>
{$allow_save=$banner|fn_allow_save_object:"banners"}
{if $allow_save}
{$no_hide_input="cm-no-hide-input"}
{else}
{$no_hide_input=""}
{/if}
<td class="left mobile-hide">
<input type="checkbox" name="banner_ids[]" value="{$banner.banner_id}" class="cm-item cm-item-status-{$banner.status|lower} hide" />
</td>
<td class="left" style="padding-left: 40px">
<a class="row-status" href="{"banners.update?banner_id=`$banner.banner_id`"|fn_url}">{$banner.banner}</a>
</td>
<td width="30%" class="left" >
{hook name="banners:manage_banner_type"}
{if $banner.type == "G"}{__("graphic_banner")}{else}{__("text_banner")}{/if}
{/hook}
</td>
<td width="15%" data-th="{__("creation_date")}">
{$banner.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
</td>
<td class="left" >
{capture name="tools_list"}
{hook name="banners:list_extra_links"}
<li>{btn type="list" text=__("edit") href="banners.update?banner_id=`$banner.banner_id`"}</li>
{if $allow_save}
<li>{btn type="list" class="cm-confirm" text=__("delete") href="banners.delete?banner_id=`$banner.banner_id`" method="POST"}</li>
{/if}
{/hook}
{/capture}
<div class="hidden-tools">
{dropdown content=$smarty.capture.tools_list}
</div>
</td>
<td width="10%" class="right" data-th="{__("status")}">
{include file="common/select_popup.tpl" id=$banner.banner_id status=$banner.status hidden=true object_id_name="banner_id" table="banners" popup_additional_class="`$no_hide_input` dropleft"}
</td>
</tr>
{/foreach}
<!--{$div_id}--></tbody>
{$sw = null}
{else}
{foreach from=$banners_tree key="key" item="banner"}
<tr {if !empty($banner.banner_id)}class="cm-row-status-{$banner.status|lower} cm-longtap-target"
{if $has_permission}
data-ca-longtap-action="setCheckBox"
data-ca-longtap-target="input.cm-item"
data-ca-id="{$banner.banner_id}"
{/if}
{/if}
>
{*GROUP*}
{if empty($banner.banner_id)}
{$href_edit="banners.update_group?group_id=`$key`&group_name=`$banner.group_name`&return_url=`$r_url`"}
<td>
&nbsp;
</td>
<td id="sw_banner{$key}" class="cm-combination cm-save-state {if $smarty.cookies["banner`$key`"]}open{/if}">
<span alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="{if $expand_all} hidden{/if}"><span class="icon-caret-right cs-dark-theme-invert"> </span></span>
<span alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="{if !$expand_all} hidden{/if}"><span class="icon-caret-down cs-dark-theme-invert"> </span></span>
<a>{$banner.group_name}</a>
</td>
<td width="30%" class="left" >{__('group')}</td>
<td class="left" >&nbsp;</td>
<td class="left" >
{capture name="tools_list"}
{hook name="banner_groups:list_extra_links"}
<li>{include file="common/popupbox.tpl" id="group`$p_feature.feature_id`" text="{__('group')} '`$banner.group_name`'" act="edit" text="{__('edit')}" href=$href_edit no_icon_link=true link_class="cm-dialog-destroy-on-close"}</li>
<li>{btn type="list" class="cm-confirm" text=__("abt__ut2.banner.disband") href="banners.delete_group?group_id=`$key`&return_url=`$r_url`" method="POST"}</li>
{/hook}
{/capture}
<div class="hidden-tools">
{dropdown content=$smarty.capture.tools_list}
</div></td>
<td width="10%" class="right" data-th="{__("status")}">
{include file="common/select_popup.tpl" id=$banner.banner_id status=$banner.status hidden=true object_id_name="banner_id" table="banners" popup_additional_class="invisible"}
</td>
{else}
{*ITEM*}
<td width="6%" class="left mobile-hide">
{$allow_save=$banner|fn_allow_save_object:"banners"}
{if $allow_save}
{$no_hide_input="cm-no-hide-input"}
{else}
{$no_hide_input=""}
{/if}
<input type="checkbox" name="banner_ids[]" value="{$banner.banner_id}" class="cm-item {$no_hide_input} cm-item-status-{$banner.status|lower} hide" /></td>
</td>
<td>
<a class="row-status" href="{"banners.update?banner_id=`$banner.banner_id`"|fn_url}">{$banner.banner}</a>
</td>
<td width="30%" class="left" >
{hook name="banners:manage_banner_type"}
{if $banner.type == "G"}{__("graphic_banner")}{else}{__("text_banner")}{/if}
{/hook}
</td>
<td width="15%" data-th="{__("creation_date")}">
{$banner.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
</td>
<td class="left" >
{capture name="tools_list"}
{hook name="banners:list_extra_links"}
<li>{btn type="list" text=__("edit") href="banners.update?banner_id=`$banner.banner_id`"}</li>
{if $allow_save}
<li>{btn type="list" class="cm-confirm" text=__("delete") href="banners.delete?banner_id=`$banner.banner_id`" method="POST"}</li>
{/if}
{/hook}
{/capture}
<div class="hidden-tools">
{dropdown content=$smarty.capture.tools_list}
</div>
</td>
<td width="10%" class="right" data-th="{__("status")}">
{include file="common/select_popup.tpl" id=$banner.banner_id status=$banner.status hidden=true object_id_name="banner_id" table="banners" popup_additional_class="`$no_hide_input` dropleft"}
</td>
{/if}
</tr>
{if $banner.items}
{include file="addons/abt__unitheme2/views/banners/components/banners_tree.tpl"
banners_tree=$banner.items
div_id = "banner`$key`"
sw = $key
}
{/if}
{/foreach}
{/if}
{else}
<p class="no-items">{__("no_data")}</p>
{/if}