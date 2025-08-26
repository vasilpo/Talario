{hook name="categories:content_views"}
<div id="extra">
{component
name="product.layout_input"
object="category"
id=$category_data.category_id|default:0
value=$category_data.product_details_view|default:"default"
input_name="category_data[product_details_view]"
company_id=$category_data.company_id
}
<div class="control-group">
<label class="control-label" for="elm_details_layout">{__("product_details_view")}:</label>
<div class="controls">
#INPUT#
</div>
</div>
{/component}
<div class="control-group">
<label class="control-label" for="elm_category_use_custom_templates">{__("use_custom_view")}:</label>
<div class="controls">
<input type="hidden" value="N" name="category_data[use_custom_templates]"/>
<input type="checkbox" class="cm-toggle-checkbox" value="Y" name="category_data[use_custom_templates]" id="elm_category_use_custom_templates"{if $category_data.selected_views} checked="checked"{/if} />
</div>
</div>
<div class="control-group">
<label class="control-label" for="elm_category_product_columns">{__("product_columns")}:</label>
<div class="controls">
<input type="text" name="category_data[product_columns]" id="elm_category_product_columns" size="10" value="{$category_data.product_columns}" class="cm-toggle-element" {if !$category_data.selected_views}disabled="disabled"{/if} />
</div>
</div>
{assign var="layouts" value=""|fn_get_products_views:false:false}
<div class="control-group">
<label class="control-label">{__("available_views")}:</label>
<div class="controls">
{foreach from=$layouts key="layout" item="item"}
<label class="checkbox" for="elm_category_layout_{$layout}"><input type="checkbox" class="cm-combo-checkbox cm-toggle-element" name="category_data[selected_views][{$layout}]" id="elm_category_layout_{$layout}" value="{$layout}" {if ($category_data.selected_views.$layout) || (!$category_data.selected_views && $item.active)}checked="checked"{/if} {if !$category_data.selected_views}disabled="disabled"{/if} />{$item.title}</label>
{/foreach}
</div>
</div>
{*
<div class="control-group">
<label class="control-label" for="elm_category_default_view">{__("default_category_view")}:</label>
<div class="controls">
<select id="elm_category_default_view" class="cm-combo-select cm-toggle-element" name="category_data[default_view]" {if !$category_data.selected_views}disabled="disabled"{/if}>
{foreach from=$layouts key="layout" item="item"}
{if ($category_data.selected_views.$layout) || (!$category_data.selected_views && $item.active)}
<option {if $category_data.default_view == $layout}selected="selected"{/if} value="{$layout}">{$item.title}</option>
{/if}
{/foreach}
</select>
</div>
</div>
*}
<table class="table table-middle table-responsive">
<thead>
<tr>
<th width="25%">{__("abt__ut2.settings.name")}</th>
<th width="25%">{__("abt__ut2.settings.value.desktop")}</th>
<th width="25%">{__("abt__ut2.settings.value.tablet")}</th>
<th width="25%">{__("abt__ut2.settings.value.mobile")}</th>
</tr>
</thead>
<tbody>
<tr id="elm_category_default_view">
<td>
<label for="elm_category_default_view_desktop">{__("default_category_view")}:</label>
</td>
{foreach ["desktop", "tablet", "mobile"] as $device_type}
<td data-th="{__("abt__ut2.settings.value.`$device_type`")}">
<select id="elm_category_default_view_{$device_type}" class="cm-combo-select cm-toggle-element" name="category_data[default_view_{$device_type}]" {if !$category_data.selected_views}disabled="disabled"{/if}>
<option {if $category_data['default_view_'|cat:$device_type] == 'inherit'}selected="selected"{/if} value="inherit">
{__('default_custom.global', ['[name]' => __("abt__ut2.settings.product_list.default_products_view.variants.`$settings.abt__ut2.product_list.default_products_view.$device_type`")])}
</option>
<option disabled="">--------------------------</option>
{foreach from=$layouts key="layout" item="item"}
{if ($category_data.selected_views.$layout) || (!$category_data.selected_views && $item.active)}
<option {if $category_data['default_view_'|cat:$device_type] == $layout}selected="selected"{/if} value="{$layout}">{$item.title}</option>
{/if}
{/foreach}
</select>
</td>
{/foreach}
</tr>
</tbody>
</table>
</div>
{/hook}