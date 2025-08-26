{$ab_stickers_hide_inputs = !"ab__stickers.edit"|fn_check_view_permissions}
<div class="hidden{if $ab_stickers_hide_inputs} cm-hide-inputs{/if}" id="content_ab__stickers">
<p class="muted">{__("ab__stickers.added_by_addon")}</p>
<div class="control-group">
<label class="control-label" for="ab__stickers_manual_stickers_{$id}">{__('ab__stickers.attached_stickers')}
{include file="common/tooltip.tpl" tooltip=__('ab__stickers.attached_stickers.product_page.tooltip')}:
</label>
<div class="controls">
{include file="addons/ab__stickers/views/ab__stickers/components/sticker_select.tpl"
id="ab__stickers_manual_stickers_{$id}"
selected=$product_data.ab__stickers_manual_ids
name="product_data[ab__stickers_manual_ids]"
}
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_generated_stickers_{$id}">{__('ab__stickers.generated_stickers')}
{include file="common/tooltip.tpl" tooltip=__('ab__stickers.generated_stickers.product_page.tooltip')}:
</label>
<div class="controls">
{if $product_data.ab__stickers_generated_ids}
<table>
<tbody>
{foreach $product_data.ab__stickers_generated_ids as $sticker_id}
<tr>
<td>
<a href="{"ab__stickers.update?sticker_id=`$sticker_id`"|fn_url}">{$ab__stickers.$sticker_id.name_for_admin}</a>
</td>
</tr>
{/foreach}
</tbody>
</table>
{else}
<span style="padding-top: 5px; display: inline-block;">{__("no_data")}</span>
{/if}
</div>
</div>
</div>