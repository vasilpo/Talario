<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive">
<thead>
<tr>
<th class="left">{__('text')}</th>
<th class="right">{__('status')}</th>
</tr>
</thead>
<tbody>
{$default_text_status = "ObjectStatuses::DISABLED"|enum}
{foreach $texts as $text_key => $text_data}
<tr class="cm-row-status-{strtolower($text_data.status|default:$default_text_status)}">
<td class="left row-status">
<a id="opener_pictogram_text-{$text_key}" title="{__('text')} {$text_data@iteration}" class="row-status cm-dialog-opener cm-ajax" href="{"ab__stickers.pictogram_text_update&sticker_id=$id&text_key=$text_key"|fn_url nofilter}" data-ca-target-id="dialog_pictogram_text-{$text_key}">{__('text')} {$text_data@iteration}</a>
<small class="muted"><br><span id="pictogram_text_description-{$text_key}">{$text_data.text|default:''}</span></small>
</td>
<td class="right">
{include file="common/select_popup.tpl" id=$id status=$text_data.status|default:$default_text_status items_status=''|fn_get_default_statuses:false statuses=$text_data.status|default:$default_text_status hidden=false update_controller="ab__stickers" extra="&text_key=$text_key" st_return_url="ab__stickers.update&sticker_id=$id"|fn_url st_result_ids="pictogram-texts-list" popup_additional_class="dropleft"}
</td>
</tr>
{/foreach}
</tbody>
</table>
</div>