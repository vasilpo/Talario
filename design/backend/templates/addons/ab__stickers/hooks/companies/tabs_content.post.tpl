{if "MULTIVENDOR"|fn_allowed_for && $addons.ab__stickers.enable_for_vendors == "YesNo::YES"|enum
&& (fn_check_view_permissions('ab__stickers.view', 'GET') || $addons.vendor_privileges.status != "ObjectStatuses::ACTIVE"|enum)}
<div id="content_ab__stickers" class="hidden">
<p class="muted">{__("ab__stickers.added_by_addon")}</p>
{if $ab__stickers}
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive">
<thead>
<tr>
<th width="2%" class="mobile-hide">{__("id")}</th>
<th width="2%" class="mobile-hide"><a class="cm-ajax" href="{"`$c_url`&sort_by=position&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents_stickers">{__("position_short")}{if $search.sort_by == "position"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
<th width="21%" class="mobile-hide"></th>
<th width="30%"><a class="cm-ajax" href="{"`$c_url`&sort_by=name_for_admin&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents_stickers">{__("name")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
<th width="25%"><a class="cm-ajax" href="{"`$c_url`&sort_by=type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents_stickers">{__("ab__stickers.params.type")}{if $search.sort_by == "type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
<th width="7%" class="right mobile-hide">&nbsp;</th>
<th width="13%" class="right"><a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents_stickers">{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
</tr>
</thead>
<tbody>
{foreach $ab__stickers as $sticker}
{$status = $sticker.vendor_status|default:$sticker.status}
<tr class="cm-row-status-{$status|lower}">
<td data-th="{__("id")}" class="muted mobile-hide ty-center">#{$sticker.sticker_id}</td>
<td data-th="{__("position_short")}" class="mobile-hide ty-center">{$sticker.position}</td>
<td class="mobile-hide ty-center">
{include file="addons/ab__stickers/views/ab__stickers/components/sticker_preview.tpl" hide_link=true}
</td>
<td data-th="{__("name")}">{$sticker.name_for_admin}</td>
<td data-th="{__("ab__stickers.params.type")}">{__("ab__stickers.params.type.`$sticker.type`")}</td>
<td></td>
<td class="right nowrap" data-th="{__("status")}">
{$tag = "a"}
{if (!fn_check_view_permissions("ab__stickers.edit", "POST")) && $addons.vendor_privileges.status == "ObjectStatuses::ACTIVE"|enum}
{$tag = "span"}
{/if}
{$change_status_href = "ab__stickers.change_vendor_status?sticker_id=`$sticker.sticker_id`&company_id=`$company_data.company_id`&result_ids=content_ab__stickers"}
{if $status == "ObjectStatuses::ACTIVE"|enum}
<{$tag} class="cm-post" data-ca-target-id="content_ab__stickers" href="{"`$change_status_href`&status=D"|fn_url}">{__("active")}</{$tag}>
{else}
<{$tag} class="cm-post" href="{"`$change_status_href`&status=A"|fn_url}">{__("disabled")}</{$tag}>
{/if}
</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="no-items">{__("no_data")}</p>
{/if}
<!--content_ab__stickers--></div>
{/if}
