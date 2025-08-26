<form action="{"ab__stickers.update_pictogram_text?sticker_id=`$sticker_id`"|fn_url}" method="post" name="form-pictogram_text-{$text_key}" enctype="multipart/form-data" id="dialog_pictogram_text-{$text_key}" class="form-horizontal form-edit cm-ajax cm-ajax-full-render">
<input type="hidden" name="result_ids" value="pictogram-texts-list">
<input type="hidden" name="redirect_url" value="{"ab__stickers.update?sticker_id=`$sticker_id`"|fn_url}">
<div class="text-controls" id="pictograms-text-controls-{$text_key}">
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_text_{$text_key}">{__("text")}<a class="cm-tooltip" title="{__("ab__stickers.pictogram.help.text")}" data-ce-tooltip-class="ab__stickers-text_tooltip">{include_ext file="common/icon.tpl" class="icon-question-sign"}</a>:</label>
<div class="controls">
<div class="input-prepend ab-input-prepend">
<input type="text" name="pictogram_data[{$text_key}][text]" id="ab__stickers_pictogram_text_text_{$text_key}" value="{$text_data.text|default:''}" class="input-large" />
<input type="checkbox" id="ab__stickers_pictogram_text_for_all_languages_{$text_key}" name="pictogram_data[for_all_languages]" value="{'YesNo::YES'|enum}">
<a class="cm-tooltip" title="{__("ab__stickers.pictogram.help.for_all_languages")}">{include_ext file="common/icon.tpl" class="icon-question-sign"}</a>
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_font_name_{$text_key}">{__("ab__stickers.font_family")}:</label>
<div class="controls">
<div class="input-prepend">
<select name="pictogram_data[{$text_key}][font][name]" id="ab__stickers_pictogram_text_font_name_{$text_key}">
{foreach fn_ab__stickers_pictograms_get_fonts() as $font_group_key => $fonts}
<optgroup label="{__("ab__stickers.pictogram.font_group.$font_group_key")}">
{foreach $fonts as $font}
<option value="{$font['path']}" {if $text_data.font.name == $font['path']} selected{/if}>{$font['name']}</option>
{/foreach}
</optgroup>
{/foreach}
</select>
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_font_size_{$text_key}">{__("ab__stickers.font_size")}:</label>
<div class="controls">
<div class="input-prepend">
<input type="number" min="6" name="pictogram_data[{$text_key}][font][size]" id="ab__stickers_pictogram_text_font_size_{$text_key}" value="{$text_data.font.size|default:8}" class="input-big" />
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_color_{$text_key}">{__("ab__stickers.text_color")}:</label>
<div class="controls">
<input type="text" name="pictogram_data[{$text_key}][color]" id="ab__stickers_pictogram_text_color_{$text_key}" value="{$text_data.color|default:'rgba(0,0,0,1)'}" data-ca-spectrum-show-alpha="true" data-ca-spectrum-preferred-format="rgb" {if $cp_storage}data-ca-storage="{$cp_storage}"{/if} class="input-large cm-colorpicker {if $cp_class}{$cp_class}{/if}" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_stroke_width_{$text_key}">{__("ab__stickers.text_stroke_width")}:</label>
<div class="controls">
<div class="input-prepend">
<input type="number" min="0" step="0.1" name="pictogram_data[{$text_key}][stroke][width]" id="ab__stickers_pictogram_text_stroke_width_{$text_key}" value="{$text_data.stroke.width|default:0}" class="input-big" />
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_stroke_color_{$text_key}">{__("ab__stickers.text_stroke_color")}:</label>
<div class="controls">
<input type="text" name="pictogram_data[{$text_key}][stroke][color]" id="ab__stickers_pictogram_text_stroke_color_{$text_key}" value="{$text_data.stroke.color|default:'rgba(255,255,255,1)'}" data-ca-spectrum-show-alpha="true" data-ca-spectrum-preferred-format="rgb" {if $cp_storage}data-ca-storage="{$cp_storage}"{/if} class="input-large cm-colorpicker {if $cp_class}{$cp_class}{/if}" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_position_vertical_{$text_key}">{__("ab__stickers.vertical_position")}:</label>
<div class="controls">
<div class="input-prepend">
<select name="pictogram_data[{$text_key}][position][vertical]" id="ab__stickers_pictogram_text_position_vertical_{$text_key}">
{foreach fn_ab__stickers_get_enum_list('PictogramPositions', 'getVerticalList') as $pictogram_vertical_position}
<option value="{$pictogram_vertical_position}"{if $text_data.position.vertical == $pictogram_vertical_position} selected{/if}>{__("ab__stickers.pictogram_vertical_position.`$pictogram_vertical_position`")}</option>
{/foreach}
</select>
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_margin_vertical_{$text_key}">{__("ab__stickers.vertical_margin")}:</label>
<div class="controls">
<div class="input-prepend">
<input type="number" min="-128" max="128" name="pictogram_data[{$text_key}][margin][vertical]" id="ab__stickers_pictogram_text_margin_vertical_{$text_key}" value="{$text_data.margin.vertical|default:0}" class="input-big" />
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_position_horizontal_{$text_key}">{__("ab__stickers.horizontal_position")}:</label>
<div class="controls">
<div class="input-prepend">
<select name="pictogram_data[{$text_key}][position][horizontal]" id="ab__stickers_pictogram_text_position_horizontal_{$text_key}">
{foreach fn_ab__stickers_get_enum_list('PictogramPositions', 'getHorizontalList') as $pictogram_horizontal_position}
<option value="{$pictogram_horizontal_position}"{if $text_data.position.horizontal == $pictogram_horizontal_position} selected{/if}>{__("ab__stickers.pictogram_horizontal_position.`$pictogram_horizontal_position`")}</option>
{/foreach}
</select>
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_margin_horizontal_{$text_key}">{__("ab__stickers.horizontal_margin")}:</label>
<div class="controls">
<div class="input-prepend">
<input type="number" min="-128" max="128" name="pictogram_data[{$text_key}][margin][horizontal]" id="ab__stickers_pictogram_text_margin_horizontal_{$text_key}" value="{$text_data.margin.horizontal|default:0}" class="input-big" />
</div>
</div>
</div>
<div class="control-group">
<label class="control-label" for="ab__stickers_pictogram_text_rotation_{$text_key}">{__("ab__stickers.rotate")}:</label>
<div class="controls">
<div class="input-prepend">
<input type="number" min="-360" max="360" step="1" name="pictogram_data[{$text_key}][rotation]" id="ab__stickers_pictogram_text_rotation_{$text_key}" value="{$text_data.rotation|default:0}" class="input-big" />
</div>
</div>
</div>
</div>
<div class="modal-footer buttons-container">
<a class="cm-dialog-closer cm-inline-dialog-closer tool-link btn">{__("cancel")}</a>
{include file="buttons/save.tpl" but_role="submit-link" but_target_form="form-pictogram_text-{$text_key}" but_meta="cm-form-dialog-closer"}
</div>
</form>