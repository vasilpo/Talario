{$condition_name="ab__stickers.conditions.names.`$condition_data.condition`"}
{$prefix_md5 = $prefix|md5}
{$condition_schema = $schema.conditions[$condition_data.condition]}
{if $condition_schema}
<div class="conditions-tree-node clearfix">
<div class="pull-right">
<a class="icon-trash cm-tooltip cm-delete-row" name="remove" id="{$item_id}" title="{__("remove")}"></a>
</div>
{if $condition_schema.template}
{include file=$condition_schema.template}
{/if}
<label for="{$prefix_md5}_condition">{__($condition_name)}{if $condition_schema.tooltip}{include file="common/tooltip.tpl" tooltip=__($condition_schema.tooltip)}{/if}&nbsp;</label>
<input type="hidden" name="{$prefix}[condition]" value="{$condition_data.condition}"/>
{if $condition_schema.variants || $condition_schema.variants_function}
{$variants = $condition_schema.variants|default:$condition_schema.variants_function|fn_ab__stickers_get_sticker_condition_variants}
{/if}
{hook name="ab__stickers:condition"}
{if $condition_schema.type == 'chained'}
<div class="select2-wrapper--width-auto">
<select name="{$prefix}[condition_element]" id="sticker_chained_condition_parent_{$prefix_md5}">
{if $condition_data.condition_element}
<option value="{$condition_data.condition_element}" selected></option>
{/if}
</select>
</div>
{/if}
{if $condition_schema.type != "list" && $condition_schema.type != "statement"}
{if $condition_schema.operators|count > 1}
<select title="" name="{$prefix}[operator]" id="sticker_condition_operator_{$prefix_md5}">
{foreach $condition_schema.operators as $operator}
<option value="{$operator}"{if $operator == $condition_data.operator} selected{/if}>
{if fn_is_lang_var_exists("promotion_op_`$operator`")}
{__("promotion_op_`$operator`")}
{else}
{__("ab__stickers.op_`$operator`")}
{/if}
</option>
{/foreach}
</select>
{else}
{$operator = reset($condition_schema.operators)}
<input type="hidden" name="{$prefix}[operator]" value="{$operator}">
{if fn_is_lang_var_exists("promotion_op_`$operator`")}
{__("promotion_op_`$operator`")}
{else}
{__("ab__stickers.op_`$operator`")}
{/if}
{/if}
{/if}
{if $condition_schema.type == "input"}
{if strpos($condition_schema.additional_classes, "cm-value-decimal") !== false || $condition_schema.decimal_format}
{$condition_data.value = sprintf($condition_schema.decimal_format|default:'%.2f', $condition_data.value)}
{/if}
{if $condition_schema.format_function && function_exists($condition_schema.format_function)}
{$condition_data.value = call_user_func($condition_schema.format_function, $condition_data.value)}
{/if}
<input id="{$prefix_md5}_condition" type="text" name="{$prefix}[value]" value="{$condition_data.value}" class="input-medium {$condition_schema.additional_classes}"/>
{elseif $condition_schema.type == "date"}
{include file="common/calendar.tpl" date_name="{$prefix}[value]" date_id="sticker_date_{$prefix_md5}" date_val=$condition_data.value|default:$smarty.const.TIME}
{elseif $condition_schema.type == "select"}
<select title="" id="{$prefix_md5}_condition" name="{$prefix}[value]">
{foreach from=$variants key="_k" item="v"}
<option value="{$_k}"
{if $_k == $condition_data.value}selected{/if}>{if $schema.conditions[$condition_data.condition].variants_function}{$v}{else}{__($v)}{/if}</option>
{/foreach}
</select>
{elseif $condition_schema.type == 'template'}
{include file=$condition_schema.condition_template variants=$variants|default:[] prefix=$prefix prefix_md5=$prefix_md5 condition_schema=$condition_schema condition_data=$condition_data}
{elseif $condition_schema.type == "picker"}
{assign var="_z" value="params_$zone"}
{if $condition_schema.picker_props.$_z}
{assign var="params" value=$condition_schema.picker_props.$_z}
{else}
{assign var="params" value=$condition_schema.picker_props.params}
{/if}
{include_ext file=$condition_schema.picker_props.picker company_ids=$picker_selected_companies data_id="objects_`$elm_id`" input_name="`$prefix`[value]" item_ids=$condition_data.value params_array=$params owner_company_id=fn_get_runtime_company_id() but_meta="btn"}
{elseif $condition_schema.type == "list"}
<input type="hidden" name="{$prefix}[operator]" value="in"/>
<input type="hidden" name="{$prefix}[value]" value="{$condition_data.value}"/>
{$condition_data.value|default:__("no_data")}
{elseif $condition_schema.type == "statement"}
<input type="hidden" name="{$prefix}[operator]" value="eq"/>
<input type="hidden" name="{$prefix}[value]" value="Y"/>
<div>{__("yes")}</div>
{elseif $condition_schema.type == 'chained'}
<div class="select2-wrapper--width-auto"{if $condition_data.operator === 'exist'} style="display: none;"{/if}>
<select name="{$prefix}[value]"{if $condition_data.operator == 'in' || $condition_data.operator == 'nin'} multiple{/if} class="hidden" id="sticker_chained_condition_child_{$prefix_md5}">
{foreach from=","|explode:$condition_data.value item="preselected_child"}
{if $preselected_child}
<option value="{$preselected_child}" selected></option>
{/if}
{/foreach}
</select>
</div>
<input id="sticker_chained_condition_child_input_{$prefix_md5}" type="text" name="{$prefix}[value]" value="{$condition_data.value}"
class="hidden input-long"{if $condition_data.operator === 'exist'} style="display: none;"{/if} />
<script>
(function (_, $) {
$(document).ready(function () {
var chainedCondition = new _.ChainedPromotionConditionForm({
operatorSelect: $('#sticker_condition_operator_{$prefix_md5}'),
parentSelect: $('#sticker_chained_condition_parent_{$prefix_md5}'),
childSelect: $('#sticker_chained_condition_child_{$prefix_md5}'),
childInput: $('#sticker_chained_condition_child_input_{$prefix_md5}'),
settings: {
parent: {
dataUrl: "{$schema.conditions[$condition_data.condition].chained_options.parent_url|fn_url}"
}
}
});
chainedCondition.render();
});
})(Tygh, Tygh.$);
</script>
{/if}
{/hook}
{if $condition_schema.available_placeholders}
<p>{__('ab__stickers.available_placeholders')}:</p>
{foreach $condition_schema.available_placeholders as $placeholder}
<p><code>{$placeholder@key}</code> – {__($placeholder.title)}</p>
{/foreach}
{/if}
</div>
{/if}