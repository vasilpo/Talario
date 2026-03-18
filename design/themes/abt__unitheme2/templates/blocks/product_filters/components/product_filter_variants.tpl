{hook name="blocks:ab__cf_variants"}
    <div class="ty-product-filters {if $collapse}hidden{/if}" id="content_{$filter_uid}">

        {if $filter.filter_style != "ProductFilterStyles::COLOR"|enum && $filter.display_count && $filter.variants|count > $filter.display_count}
            {script src="js/tygh/filter_table.js"}
            <div class="ut2-filters__search">
                <div class="ty-product-filters__search">
                    <input type="text" placeholder="{__("storefront_search_field")}" class="cm-autocomplete-off ty-input-text-medium" name="q" id="elm_search_{$filter_uid}" value="" />
                    {include_ext file="common/icon.tpl"
                    class="ty-icon-cancel-circle ty-product-filters__search-icon hidden"
                    id="elm_search_clear_`$filter_uid`"
                    title=__("clear")
                    }
                </div>
            </div>
        {/if}

        {$white_color = "#ffffff"}

        {if $filter.variants}
            {if $filter.filter_style == "ProductFilterStyles::COLOR"|enum}
                {script src="js/addons/abt__unitheme2/abt__ut2_product_filters.js"}
                    <ul class="ty-product-filters__variants ty-product-filters__color-filter" {if $filter.display_count}style="--ut2-product-filter-shortlist-variant-count: {$filter.display_count};"{/if}>
                        {foreach from=$filter.variants  item=variant name=color_variants}
                            <li class=" cm-product-filters-checkbox-container {if $smarty.foreach.color_variants.iteration > $filter.display_count}hidden{else}ty-product-filters__color-list-item{/if}">
                                <label
                                        id="elm_checkbox_{$filter_uid}_{$variant.variant_id}"
                                        name="product_filters[{$filter.filter_id}]"
                                        class="ty-product-filters__color-filter-item
                            {if $variant.disabled}{if $variant.selected}ty-product-filters__color-filter-item--selected{else}ty-product-filters__color-filter-item--disabled{/if}{/if}"
                                        data-cm-product-color-filter="true"
                                        data-ca-product-color-filter-checkbox-id="elm_checkbox_{$filter_uid}_{$variant.variant_id}"
                                        title="{$filter.prefix}{$variant.variant|fn_text_placeholders}{$filter.suffix}">
                                    <input
                                            class="cm-product-filters-checkbox ty-product-filters__color-filter-checkbox"
                                            type="checkbox"
                                            data-ca-filter-id="{$filter.filter_id}"
                                            value="{$variant.variant_id}"
                                            id="elm_checkbox_{$filter_uid}_{$variant.variant_id}"
                                            {if $variant.selected}checked{/if}
                                            {if $variant.disabled && !$variant.selected}disabled="disabled"{/if} />
                                    {$variant_selected_class = ($variant.selected) ? "ty-product-filters__color-filter-check--selected" : ""}
                                    {$variant_color_class = ($variant.color === $white_color) ? "ty-product-filters__color-filter-check--invert" : ""}
                                    {include_ext file="common/icon.tpl"
                                    class="ty-icon-ok ty-product-filters__color-filter-check `$variant_selected_class` `$variant_color_class`"
                                    }
                                    {$color1 = $variant.color|default:$white_color}
                                    {$color2 = ($variant.abt__ut2_color_style === 'multicolor') ? ($variant.abt__ut2_multicolor|default:$color1) : $color1}
                                    <div class="ty-product-filters__color-filter-swatch {if $variant.selected}ty-product-filters__color-filter-swatch--selected{/if}"
                                            style="background: linear-gradient(120deg,{$color1} 50%, {$color2} 51%)">
                                        {if $variant.abt__ut2_color_style === 'thumbnail'}
                                            {include file="common/image.tpl" images=$variant.image_pair image_width=64 height=64 no_ids=true lazy_load=false}
                                        {/if}
                                    </div>
                                </label>
                            </li>
                        {/foreach}
                    </ul>
                    {if $smarty.foreach.color_variants.total > $filter.display_count}
                        <a class="ut2-more-btn"
                           data-toggle-color-filter
                           data-display-count="{$filter.display_count|default:0}"
                        >
                            <i class="ut2-icon-outline-expand_more"></i>
                            <span class="more">{__('show_all')} ({$filter.variants|@count})</span>
                            <span class="hidden">{__('abt__ut2_btn_filters_collapse')}</span>
                        </a>
                    {/if}
            {else}
                    <ul id="ranges_{$filter_uid}" {if $filter.display_count}style="--ut2-product-filter-variant-list-_max-height_-shortened: {$filter.display_count * 31}px;"{/if} class="ty-product-filters__variants {if $filter.variants|@count > $filter.display_count}items-more {/if}cm-filter-table" data-ca-input-id="elm_search_{$filter_uid}" data-ca-clear-id="elm_search_clear_{$filter_uid}" data-ca-empty-id="elm_search_empty_{$filter_uid}">

                        {foreach $filter.variants as $variant}
                            <li class="cm-product-filters-checkbox-container ty-product-filters__group ut2-product-filters__variant">
                                <input class="cm-product-filters-checkbox" type="checkbox" {if $variant.selected}checked="checked"{/if} name="product_filters[{$filter.filter_id}]" data-ca-filter-id="{$filter.filter_id}" value="{$variant.variant_id}" id="elm_checkbox_{$filter_uid}_{$variant.variant_id}" {if $variant.disabled && !$variant.selected}disabled="disabled"{/if}><label {if $variant.disabled}class="{if $variant.selected}ty-product-filters__empty-result{else}disabled{/if}"{/if} for="elm_checkbox_{$filter_uid}_{$variant.variant_id}"><span><span class="ut2-product-filters__variant__prefix">{$filter.prefix}</span><span class="ut2-product-filters__variant__value">{$variant.variant|fn_text_placeholders}</span><span class="ut2-product-filters__variant__suffix">{$filter.suffix}</span></span></label>
                            </li>
                        {/foreach}
                    </ul>
                    {if $filter.variants|@count > $filter.display_count}
                        <a class="ut2-more-btn" onclick="$(this).prev().toggleClass('none-overflow'); $(this).toggleClass('open');"><i class="ut2-icon-outline-expand_more"></i><span class="more">{__('show_all')} ({$filter.variants|@count})</span><span class="hidden">{__('abt__ut2_btn_filters_collapse')}</span></a>
                    {/if}
            {/if}
            <div id="elm_search_empty_{$filter_uid}" class="ty-product-filters__no-items-found hidden">{__("no_items_found")}</div>
        {/if}
    </div>
{/hook}