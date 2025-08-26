<div class="group form-horizontal">
<div class="control-group">
<a href="#" class="search-link cm-combination cm-save-state open" id="sw_ab__stickers_product">
<span id="on_ab__stickers_product" class="icon-caret-right cm-save-state{if $smarty.cookies.ab__stickers_product} hidden{/if}"> </span>
<span id="off_ab__stickers_product" class="icon-caret-down cm-save-state{if !$smarty.cookies.ab__stickers_product} hidden{/if}"></span>
{__('ab__stickers.search_by')}
</a>
<div id="ab__stickers_product"{if !$smarty.cookies.ab__stickers_product} class="hidden"{/if}>
<div class="table-wrapper table-responsive-wrapper">
<table class="table-responsive table-responsive--nopadding table-responsive--centered">
<thead>
<tr>
<td><strong>{__('ab__stickers.attached_stickers')}</strong></td>
<td><strong>{__('ab__stickers.generated_stickers')}</strong></td>
</tr>
</thead>
<tbody>
<tr class="delim" valign="top">
<td data-th="{__('ab__stickers.attached_stickers')}" width="350px">
{include file="addons/ab__stickers/views/ab__stickers/components/sticker_select.tpl"
id="ab__stickers_manual_stickers"
selected=$search.ab__stickers_manual_ids
name="ab__stickers_manual_ids"
}
</td>
<td data-th="{__('ab__stickers.generated_stickers')}" width="350px">
{include file="addons/ab__stickers/views/ab__stickers/components/sticker_select.tpl"
id="ab__stickers_generated_stickers"
selected=$search.ab__stickers_generated_ids
name="ab__stickers_generated_ids"
}
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</div>