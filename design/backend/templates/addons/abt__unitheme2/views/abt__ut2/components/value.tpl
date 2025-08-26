{strip}
{$f_v="abt__unitheme2_data[`$f_section`][`$f_group``$f_name`]"}
{$f_disabled=""}
{if !$f_type}
{$f_v="abt__unitheme2_data[`$f_section`][`$f_group``$f_name`]"}
{$f_value = $f.value|default:""}
{else}
{$f_v="abt__unitheme2_data[`$f_section`][`$f_group``$f_name`][`$f_type`]"}
{$f_value = $f.value.$f_type|default:""}
{$f_id="`$f_id`.`$f_type`"}
{if $f.disabled.$f_type == "YesNo::YES"|enum}{$f_disabled='disabled="disabled"'}{/if}
{/if}
{if !$f.attrs}
{$f.attrs = []}
{/if}
{** Checkbox **}
{if $f.type == "checkbox"}
<input {$f.attrs|render_tag_attrs nofilter} type="hidden" value="N" name="{$f_v}">
<input{if $label} title="{$label}"{/if} {$f.attrs|render_tag_attrs nofilter} id="{$f_id}" type="checkbox" value="Y" name="{$f_v}"{if $f_value == "YesNo::YES"|enum} checked="checked"{/if} {$f_disabled}>
{** selectbox **}
{elseif $f.type == "selectbox"}
<select{if $label} title="{$label}"{/if} id="{$f_id}" {$f.attrs|render_tag_attrs nofilter} name="{$f_v}" class="{$f.class|default:"span10"}" {$f_disabled}>
{foreach from=$f.variants item="v"}
{if is_array($v)}{$options = $v}{$v = $v@key}{/if}
<option value="{$v}"{if $v == $f_value} selected="selected"{/if}{if $options.tooltip} title="{__($options.tooltip)}"{/if}>
{if $f.variants_as_language_variable|default:{"YesNo::YES"|enum} == "YesNo::YES"|enum}
{__({"`$ls`.`$f_section`.`$f_group``$f_name`.variants.`$v`"})}
{else}
{$v}
{/if}
</option>
{/foreach}
</select>
{if $f.suffix}&nbsp;{$f.suffix}{/if}
{** input **}
{elseif $f.type == "input"}
<input{if $label} title="{$label}"{/if} {$f.attrs|render_tag_attrs nofilter} id="{$f_id}" type="text" name="{$f_v}" value="{$f_value}" class="cm-trim {$f.class|default:"span10"}" {$f_disabled}>
{if $f.suffix}&nbsp;{$f.suffix}{/if}
{** textarea **}
{elseif $f.type == "textarea"}
<textarea{if $label} title="{$label}"{/if} id="{$f_id}" {$f.attrs|render_tag_attrs nofilter} name="{$f_v}" class="cm-trim {$f.class|default:"span10"}" {$f_disabled}>{$f_value}</textarea>
{** colorpicker **}
{elseif $f.type == "colorpicker"}
{include file="views/theme_editor/components/colorpicker.tpl" cp_name=$f_v cp_id="storage_elm_te_`$section`_`$f.name`" cp_value=$f_value|replace:"transparent":""|default:"#ffffff"}
{/if}
{/strip}
