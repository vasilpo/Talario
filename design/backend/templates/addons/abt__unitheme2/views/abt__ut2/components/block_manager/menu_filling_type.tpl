<select name="{$html_name}" id="{$html_id}" title="{__($name)}">
{$value = $value|default:$option.default_value}
{foreach $option.values as $variant}
<option value="{$variant@key}"{if $value == $variant@key} selected{/if}>{__("abt__ut2.menu.filling_variants.{$variant@key}")}</option>
{/foreach}
</select>
<script>
(function(_, $) {
var select = $("#{$html_id}");
{$tmp = []}
{foreach $option.values as $variant}
{$tmp = $tmp|array_merge:$variant.show_fields}
{/foreach}
var hide_fileds = [
{foreach $tmp as $field}
'#block_{$block.block_id}_{$block.snapping_id}_menu_properties_{$field}'{if !$filed@last},{/if}
{/foreach}
].join(',');
$(hide_fileds).each(function(){
var field = $(this);
field.parents(".control-group").hide();
});
select.on("change", function() {
var self = $(this);
var selected = self.find("option:selected");
{foreach $option.values as $variant}
if (selected.val() === '{$variant@key}') {
var show_fields = [
{foreach $variant.show_fields as $field}
'#block_{$block.block_id}_{$block.snapping_id}_menu_properties_{$field}'{if !$filed@last},{/if}
{/foreach}
].join(',');
$(show_fields).each(function(){
var field = $(this);
field.parents(".control-group").show();
});
}
{/foreach}
}).change();
}(Tygh, Tygh.$));
</script>