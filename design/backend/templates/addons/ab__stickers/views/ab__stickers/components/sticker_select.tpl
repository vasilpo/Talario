{if $selected}
<input type="hidden" name="{$name}" value=""/>
{/if}
<div class="object-selector" style="width: 320px;">
<select id="{$id}"
class="cm-object-selector"
name="{$name}[]"
multiple
data-ca-load-via-ajax="true"
data-ca-placeholder="{__("ab__stickers.find_available_stickers")}"
data-ca-enable-search="true"
data-ca-enable-images="true"
data-ca-image-width="30"
data-ca-image-height="30"
data-ca-close-on-select="false"
data-ca-page-size="100"
data-ca-data-url="{"ab__stickers.get_stickers_objects"|fn_url nofilter}">
{if $selected}
{foreach $selected as $sticker_id}
<option value="{$sticker_id}" selected="selected">{$ab__stickers.$sticker_id.name_for_admin}</option>
{/foreach}
{/if}
</select>
</div>