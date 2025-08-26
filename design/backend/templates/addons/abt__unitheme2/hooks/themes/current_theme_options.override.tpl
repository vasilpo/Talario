{if $theme.theme_name == 'abt__unitheme2'}
<span class="muted">{__("theme_styles_and_layouts")}</span>
<div class="table-wrapper">
<table class="table table-middle table--relative">
<thead>
<tr>
<th>{__("layout")}</th>
<th>{__("theme_editor.style")}</th>
<th> </th>
<th> </th>
</tr>
</thead>
<tbody>
{$has_styles = !!$theme.styles}
{foreach $theme.layouts as $available_layout}
<tr>
<td>{$available_layout.name}</td>
<td>
{$styles_descr = []}
{foreach $available_themes.current.styles as $style}
{$styles_descr[$style.style_id] = $style.name}
{/foreach}
{if $has_styles}
{include file="common/select_popup.tpl" id=$available_layout.layout_id status=$available_layout.style_id items_status=$styles_descr update_controller="themes.styles" status_target_id="theme_description_container,themes_list" statuses=$available_themes.current.styles btn_meta="btn-text o-status-`$o.status`"|lower default_status_text=__("none")}
{else}
<span class="muted">{__("theme_no_styles_text")}</span>
{/if}
</td>
<td>
{capture name="tools_list"}
<li>{btn type="list" text=__('abt__ut2.settings') href="abt__ut2.settings"}</li>
<li>{btn type="list" text="{__('abt__ut2.less_settings')} {$available_layout.style_id}" href="abt__ut2.less_settings?style={$available_layout.style_id}.less"}</li>
{/capture}
{dropdown content=$smarty.capture.tools_list}
</td>
<td class="right btn-toolbar btn-toolbar--theme-editor">
{if $available_layout.is_default}
{$but_meta = "btn-small btn-primary cm-post"}
{else}
{$but_meta = "btn-small cm-post"}
{/if}
{if $has_styles}
{include file="buttons/button.tpl"
but_href="customization.update_mode?type=theme_editor&status=enable&s_layout={$available_layout.layout_id}&s_storefront={$storefront->storefront_id}"
but_text=__("theme_editor")
but_role="action"
but_meta=$but_meta
but_target="_blank"
}
{else}
{include file="buttons/button.tpl"
title=__("theme_editor_not_supported")
but_text=__("theme_editor")
but_role="btn"
but_meta="btn btn-small disabled cm-tooltip"
}
{/if}
{include file="buttons/button.tpl"
but_href="customization.update_mode?type=block_manager&status=enable&s_layout={$available_layout.layout_id}&s_storefront={$storefront->storefront_id}"
but_text=__("edit_layout_on_site")
but_role="action"
but_meta=$but_meta
but_target="_blank"
}
{include file="buttons/button.tpl"
but_href="customization.update_mode?type=live_editor&status=enable&s_layout={$available_layout.layout_id}&s_storefront={$storefront->storefront_id}"
but_text=__("edit_content_on_site")
but_role="action"
but_meta=$but_meta
but_target="_blank"
}
</td>
<tr>
{/foreach}
</tbody>
</table>
</div>
{/if}