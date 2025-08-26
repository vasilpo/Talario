{** fonts section **}
{strip}
{capture name="mainbox"}
{$hide_inputs = !"ab__stickers.edit"|fn_check_view_permissions}
<form action="{""|fn_url}" method="post" name="ab__stickers_fonts_form" enctype="multipart/form-data"{if $hide_inputs} class="cm-hide-inputs"{/if}>
<input type="hidden" name="fake" value="1" />
{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id="pagination_contents_stickers_fonts"}
{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents_stickers_fonts"}
{assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}
{if $fonts}
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive">
<thead>
<tr>
<th width="1%" class="left">
{include file="common/check_items.tpl"}
</th>
<th width="80%">{__("name")}</th>
<th width="10%" class="right mobile-hide">&nbsp;</th>
</tr>
</thead>
{foreach from=$fonts key=fonts_group_key item=fonts_group}
<tbody>
{foreach $fonts_group as $font}
<tr class="cm-row">
<td class="left mobile-hide">
{if $fonts_group_key !== 'default'}
<input type="checkbox" name="fonts_paths[]" value="{$font.path}" class="cm-item" />
{/if}
</td>
<td data-th="{__('name')}">{$font.name}</td>
<td class="mobile-hide">
{capture name="tools_list"}
{if !$hide_inputs && $fonts_group_key !== 'default'}
<li>{btn type="list" class="cm-confirm" text=__("delete") href="ab__stickers.font_delete?fonts_paths[]=`$font.path`" method="POST"}</li>
{/if}
{/capture}
<div class="hidden-tools">
{dropdown content=$smarty.capture.tools_list}
</div>
</td>
</tr>
{/foreach}
</tbody>
{/foreach}
</table>
</div>
{else}
<p class="no-items">{__("no_data")}</p>
{/if}
{include file="common/pagination.tpl" div_id="pagination_contents_stickers"}
{capture name="buttons"}
{if $fonts && !$hide_inputs}
{capture name="tools_list"}
<li>{btn type="delete_selected" dispatch="dispatch[ab__stickers.font_delete]" form="ab__stickers_fonts_form"}</li>
{if "DEVELOPMENT"|defined && $smarty.const.DEVELOPMENT == true}
<li class="divider"></li>
<li><a class="cm-submit cm-confirm" data-ca-target-form="ab__stickers_fonts_form" data-ca-dispatch="dispatch[ab__stickers.m_font_delete.all]">{__('ab__stickers.pictogram.fonts.delete_all')}</a></li>
{/if}
{/capture}
{dropdown content=$smarty.capture.tools_list}
{/if}
{/capture}
{if !$hide_inputs}
{capture name="adv_buttons"}
{capture name="add_new_picker_2"}
{include file="addons/ab__stickers/views/ab__stickers/font_update.tpl" font=[] in_popup=true return_url=$smarty.request.return_url}
{/capture}
{include file="common/popupbox.tpl" id="add_new_font" text=__("ab__stickers.pictogram.fonts.add_font") title=__("ab__stickers.pictogram.fonts.add_font") content=$smarty.capture.add_new_picker_2 act="general" icon="icon-plus" hide_tools=true}
{/capture}
{/if}
</form>
{/capture}
{capture name="mainbox"}
{$smarty.capture.mainbox nofilter}
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__stickers"}
{include file="common/mainbox.tpl"
title_start=__("ab__stickers")|truncate:40
title_end=__("ab__stickers.pictogram.fonts")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
select_languages=false
select_storefront=false
show_all_storefront=false}
{/strip}