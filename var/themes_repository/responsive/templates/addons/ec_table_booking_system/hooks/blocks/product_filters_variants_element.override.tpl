{if $filter.slider}
    {if $filter.feature_type == "ProductFeatures::DATE"|enum}
        {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
    {else}
        {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
    {/if}
{elseif $filter.field_type == "B"}
    {$min = $smarty.const.TIME}
    {$max = $filter.max}
    {$left = $filter.variants.left|default:$min}
    {$right = $filter.variants.right|default:$max}
    <div class="cm-product-filters-checkbox-container {if $collapse}hidden{/if}" id="content_{$filter_uid}">
        {include
            file="common/daterange_picker.tpl"
            id="range_`$filter_uid`"
            start_date=$left
            end_date=$right
            show_ranges=false
            data_event="ce.filterdate"}

        <input id="elm_checkbox_range_{$filter_uid}"
            data-ca-filter-id="{$filter.filter_id}"
            class="cm-product-filters-checkbox hidden"
            type="checkbox"
            name="product_filters[{$filter.filter_id}]" value="" />
    </div>
{else}
    {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter collapse=$collapse}
{/if}