<!--override module sd_home_filters-->
{** block-description:horizontal_filters **}

{if $block.type == "product_filters" || ($block.type == "product_filters_home" && $block.properties.sd_apply_button == "YesNo::NO"|enum)}
    {script src="js/tygh/product_filters.js"}
{/if}
{script src="js/addons/sd_home_filters/home_filters.js"}

{if $block.type == "product_filters"}
    {$ajax_div_ids = "product_filters_*,selected_filters_*,products_search_*,category_products_*,currencies_*,languages_*,product_features_*"}
    {$curl = $config.current_url}
{else}
    {$curl = "products.search"|fn_url}
    {$ajax_div_ids = ""}
{/if}

{$filter_base_url = $curl|fn_query_remove:"result_ids":"full_render":"filter_id":"view_all":"req_range_id":"features_hash":"subcats":"page":"total"}
{$id="product_filters_{$block.block_id}"}
{$elements_to_scroll = 1}
{$sd_home_filters_city_filter_id = $addons.sd_home_filters.city_filter_id|default:""}
{$sd_home_filters_age_filter_id = $addons.sd_home_filters.age_filter_id|default:""}
{$sd_home_filters_category_filter_id = $addons.sd_home_filters.category_filter_id|default:""}
{$sd_home_filters_products_filter_uid = "`$block.block_id`_products"}
{$sd_home_filters_products_dropdown_inserted = false}

<div class="ty-horizontal-product-filters cm-product-filters cm-horizontal-filters ut2-scroll-container ut2-filters"
     data-ca-target-id="{$ajax_div_ids}"
     data-ca-base-url="{$filter_base_url|fn_url}"
     data-ca-tooltip-class = "ty-product-filters__tooltip"
     data-ca-tooltip-right-class = "ty-product-filters__tooltip--right"
     data-ca-tooltip-mobile-class = "ty-tooltip--mobile"
     data-ca-tooltip-layout-selector = "[data-ca-tooltip-layout='true']"
     data-ce-tooltip-events-tooltip = "mouseenter"
     data-ca-home-filters-block-id="{$block.block_id}"
     data-ca-home-filters-request-endpoint="{"sd_home_filters.get_products"|fn_url}"
     data-ca-home-filters-city-filter-id="{$sd_home_filters_city_filter_id}"
     data-ca-home-filters-age-filter-id="{$sd_home_filters_age_filter_id}"
     data-ca-home-filters-category-filter-id="{$sd_home_filters_category_filter_id}"
     data-ca-home-filters-products-switch-id="sw_elm_filter_{$sd_home_filters_products_filter_uid}"
     data-ca-home-filters-apply-button-id="apply_filters_btn_{$block.block_id}"
     id="product_filters_{$block.block_id}">
    <button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>
    <div class="ty-product-filters__wrapper ut2-scroll-content">
        {if $items}

            {foreach from=$items item="filter" name="filters"}

                {$filter_uid = "`$block.block_id`_`$filter.filter_id`"}

                {$reset_url = ""}
                {if $filter.selected_variants || $filter.selected_range}
                    {$reset_url = $filter_base_url}
                    {$fh = $smarty.request.features_hash|fn_delete_filter_from_hash:$filter.filter_id}
                    {if $fh}
                        {$reset_url = $filter_base_url|fn_link_attach:"features_hash=$fh"}
                    {/if}
                {/if}

                <div class="ut2__horizontal-product-filters-dropdown ut2-scroll-item">
                    <div id="sw_elm_filter_{$filter_uid}" class="ty-horizontal-product-filters-dropdown__wrapper {if $settings.ab__device !== 'desktop'}cm-abt--ut2-toggle-scroll{/if} cm-combination {if $filter.selected_variants || $filter.selected_range}active{/if}{if $filter.selected_variants|sizeof > 0} selected{/if}">{$filter.filter}{if $filter.selected_variants}<span>{$filter.selected_variants|sizeof}</span>{/if}<i class="ty-horizontal-product-filters-dropdown__icon ty-icon-down-micro"></i></div>
                    {if $settings.ab__device !== 'mobile'}
                        <div id="elm_filter_{$filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content cm-horizontal-filters-content cm-smart-position-h">
                            <div class="ty-horizontal-product-filters-dropdown__title">
                                <a href="javascript:void(0);" rel="nofollow" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$filter_uid}"><i class="ut2-icon-baseline-close"></i></span></a>
                            </div>
                            {hook name="blocks:product_filters_variants_element"}
                            {if $filter.slider}
                                {if $filter.feature_type == "ProductFeatures::DATE"|enum}
                                    {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
                                {else}
                                    {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
                                {/if}
                            {else}
                                {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter}
                            {/if}
                            {/hook}
                            {strip}
                                <div class="ty-product-filters__tools">
                                    {if $reset_url}<a class="ty-btn ty-btn__primary outline ty-product-filters__reset-button  cm-ajax cm-ajax-full-render cm-history" href="{$reset_url|fn_url}" data-ca-event="ce.filtersinit" data-ca-target-id="{$ajax_div_ids}"><i class="ty-product-filters__reset-icon ty-icon-cw"></i>{__("reset")}</a>{/if}
                                </div>
                            {/strip}
                        </div>
                    {/if}
                </div>

                {if !$sd_home_filters_products_dropdown_inserted && $filter.filter_id == $sd_home_filters_category_filter_id}
                    {$sd_home_filters_products_dropdown_inserted = true}
                    <div class="ut2__horizontal-product-filters-dropdown ut2-scroll-item">
                        <div id="sw_elm_filter_{$sd_home_filters_products_filter_uid}" class="ty-horizontal-product-filters-dropdown__wrapper {if $settings.ab__device !== 'desktop'}cm-abt--ut2-toggle-scroll{/if} cm-combination">{"sd_home_filters.products_filter_title"|__}<span data-ca-subcategory-count class="hidden"></span><i class="ty-horizontal-product-filters-dropdown__icon ty-icon-down-micro"></i></div>
                        {if $settings.ab__device !== 'mobile'}
                            <div id="elm_filter_{$sd_home_filters_products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content custom-content cm-horizontal-filters-content cm-smart-position-h">
                                <a href="javascript:void(0);" rel="nofollow" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$filter_uid}"><i class="ut2-icon-baseline-close"></i></span></a>
                                <div id="sd_home_filters_products_{$block.block_id}"
                                    class="cm-sd-home-filters-products"
                                    data-ca-default-text='{"sd_home_filters.apply_filters_placeholder"|__}'
                                    data-ca-empty-text='{"sd_home_filters.no_products"|__}'
                                    data-ca-loading-text='{"sd_home_filters.loading_products"|__}'>
                                    <div class="ty-product-filters__item-more">
                                        <ul class="ty-product-filters__variants">
                                            <li class="ty-product-filters__group">
                                                <span>{"sd_home_filters.apply_filters_placeholder"|__}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                {/if}

            {/foreach}

            {if !$sd_home_filters_products_dropdown_inserted}
                <div class="ut2__horizontal-product-filters-dropdown ut2-scroll-item">
                    <div id="sw_elm_filter_{$sd_home_filters_products_filter_uid}" class="ty-horizontal-product-filters-dropdown__wrapper {if $settings.ab__device !== 'desktop'}cm-abt--ut2-toggle-scroll{/if} cm-combination">{"sd_home_filters.products_filter_title"|__}<span data-ca-subcategory-count class="hidden"></span><i class="ty-horizontal-product-filters-dropdown__icon ty-icon-down-micro"></i></div>
                    {if $settings.ab__device !== 'mobile'}
                        <div id="elm_filter_{$sd_home_filters_products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content custom-content cm-horizontal-filters-content cm-smart-position-h">

                            <div id="sd_home_filters_products_{$block.block_id}"
                                class="cm-sd-home-filters-products"
                                data-ca-default-text='{"sd_home_filters.apply_filters_placeholder"|__}'
                                data-ca-empty-text='{"sd_home_filters.no_products"|__}'
                                data-ca-loading-text='{"sd_home_filters.loading_products"|__}'>
                                <div class="ty-product-filters__item-more">
                                    <ul class="ty-product-filters__variants">
                                        <li class="ty-product-filters__group">
                                            <span>{"sd_home_filters.apply_filters_placeholder"|__}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
            {/if}

            {if $settings.ab__device === "mobile"}
                {$sd_home_filters_products_dropdown_inserted = false}
                {capture name="products_horizontal_filters_content"}
                    {foreach from=$items item="filter" name="filters"}

                        {$filter_uid = "`$block.block_id`_`$filter.filter_id`"}

                        {$reset_url = ""}
                        {if $filter.selected_variants || $filter.selected_range}
                            {$reset_url = $filter_base_url}
                            {$fh = $smarty.request.features_hash|fn_delete_filter_from_hash:$filter.filter_id}
                            {if $fh}
                                {$reset_url = $filter_base_url|fn_link_attach:"features_hash=$fh"}
                            {/if}
                        {/if}

                        <div id="elm_filter_{$filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content cm-horizontal-filters-content">
                            <div class="ty-horizontal-product-filters-dropdown__title">
                                <span>{$filter.filter}</span>
                                <a href="javascript:void(0);" rel="nofollow" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$filter_uid}"><i class="ut2-icon-baseline-close"></i></span></a>
                            </div>
                            {hook name="blocks:product_filters_variants_element"}
                            {if $filter.slider}
                                {if $filter.feature_type == "ProductFeatures::DATE"|enum}
                                    {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
                                {else}
                                    {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
                                {/if}
                            {else}
                                {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter}
                            {/if}
                            {/hook}
                            {strip}
                                <div class="ty-product-filters__tools">
                                    {if $reset_url}<a class="ty-btn ty-btn__primary outline ty-product-filters__reset-button  cm-ajax cm-ajax-full-render cm-history" href="{$reset_url|fn_url}" data-ca-event="ce.filtersinit" data-ca-target-id="{$ajax_div_ids}"><i class="ty-product-filters__reset-icon ty-icon-cw"></i>{__("reset")}</a>{/if}
                                </div>
                            {/strip}
                        </div>

                        {if !$sd_home_filters_products_dropdown_inserted && $filter.filter_id == $sd_home_filters_category_filter_id}
                            {$sd_home_filters_products_dropdown_inserted = true}
                            <div id="elm_filter_{$sd_home_filters_products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content cm-horizontal-filters-content">
                                <div class="ty-horizontal-product-filters-dropdown__title">
                                    <span>{"sd_home_filters.products_filter_title"|__}</span>
                                    <a type="button" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$sd_home_filters_products_filter_uid}"><i class="ut2-icon-baseline-close"></i></a>
                                </div>
                                <div id="sd_home_filters_products_{$block.block_id}"
                                    class="cm-sd-home-filters-products"
                                    data-ca-default-text='{"sd_home_filters.apply_filters_placeholder"|__}'
                                    data-ca-empty-text='{"sd_home_filters.no_products"|__}'
                                    data-ca-loading-text='{"sd_home_filters.loading_products"|__}'>
                                    <div class="ty-product-filters__item-more">
                                        <ul class="ty-product-filters__variants">
                                            <li class="ty-product-filters__group">
                                                <span>{"sd_home_filters.apply_filters_placeholder"|__}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        {/if}

                    {/foreach}

                    {if !$sd_home_filters_products_dropdown_inserted}
                        <div id="elm_filter_{$sd_home_filters_products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content cm-horizontal-filters-content">
                            <div class="ty-horizontal-product-filters-dropdown__title">
                                <span>{"sd_home_filters.products_filter_title"|__}</span>
                                <button type="button" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$sd_home_filters_products_filter_uid}"><i class="ut2-icon-baseline-close"></i></button>
                            </div>
                            <div id="sd_home_filters_products_{$block.block_id}"
                                class="cm-sd-home-filters-products"
                                data-ca-default-text='{"sd_home_filters.apply_filters_placeholder"|__}'
                                data-ca-empty-text='{"sd_home_filters.no_products"|__}'
                                data-ca-loading-text='{"sd_home_filters.loading_products"|__}'>
                                <div class="ty-product-filters__item-more">
                                    <ul class="ty-product-filters__variants">
                                        <li class="ty-product-filters__group">
                                            <span>{"sd_home_filters.apply_filters_placeholder"|__}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    {/if}
                {/capture}
            {/if}
        {/if}
    </div>
        {if $block.type == "product_filters_home" && $block.properties.sd_apply_button == "YesNo::YES"|enum}
            <div class="ty-horizontal-product-filters__apply-inline">
                <button type="button"
                        id="apply_filters_btn_{$block.block_id}"
                        class="ty-btn ty-btn__primary">
                    {__("sd_home_filters.show_results")}
                </button>
            </div>
        {/if}
    <button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>

    {if $settings.ab__device === "mobile"}
        {$smarty.capture.products_horizontal_filters_content nofilter}
    {/if}

    {include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll|default: 2}

    <!--product_filters_{$block.block_id}--></div>
<div data-ca-tooltip-layout="true" class="hidden">
    <button type="button" data-ca-scroll=".main-content-grid" class="cm-scroll ty-tooltip--link ty-tooltip--filter"><span class="tooltip-arrow"></span></button>
</div>
