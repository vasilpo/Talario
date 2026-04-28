{if $filter_data.filters}
    {$id = "lr_homepage_search_filters_`$filter_data.block_id`"}
    {$elements_to_scroll = 1}
    {$products_filter_uid = "`$filter_data.block_id`_products"}
    {$products_dropdown_inserted = false}

    <div class="ty-horizontal-product-filters cm-lr-homepage-search-filters cm-horizontal-filters ut2-scroll-container ut2-filters"
         id="{$id}"
         data-ca-lr-homepage-search-filters="true"
         data-ca-lr-homepage-search-filters-url="{$filter_data.search_url|escape}"
         data-ca-lr-homepage-search-filters-request-endpoint="{$filter_data.products_endpoint|escape}"
         data-ca-lr-homepage-search-filters-category-filter-id="{$filter_data.category_filter_id}"
         data-ca-lr-homepage-search-filters-products-limit="{$filter_data.products_dropdown_limit}"
         data-ca-lr-homepage-search-filters-products-switch-id="sw_elm_filter_{$products_filter_uid}">
        <button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>
        <div class="ty-product-filters__wrapper ut2-scroll-content">
            {foreach from=$filter_data.filters item="filter"}
                {$filter_uid = "`$filter_data.block_id`_`$filter.filter_id`"}
                {$is_age_filter = $filter.key == "age"}

                <div class="ut2__horizontal-product-filters-dropdown ut2-scroll-item">
                    <div id="sw_elm_filter_{$filter_uid}"
                         class="ty-horizontal-product-filters-dropdown__wrapper {if $settings.ab__device !== 'desktop'}cm-abt--ut2-toggle-scroll{/if} cm-combination"
                         data-ca-lr-homepage-search-filters-switch="true"
                         data-ca-filter-id="{$filter.filter_id}">
                        {$filter.title|escape}<span class="hidden" data-ca-lr-homepage-search-filters-count></span><i class="ty-horizontal-product-filters-dropdown__icon ty-icon-down-micro"></i>
                    </div>
                    {if $settings.ab__device !== "mobile"}
                        <div id="elm_filter_{$filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content cm-horizontal-filters-content cm-smart-position-h {if $is_age_filter}lr-homepage-search-filters__age-filter{/if}">
                            <div class="ty-horizontal-product-filters-dropdown__title">
                                <div  class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$filter_uid}"><i class="ut2-icon-baseline-close"></i></div>
                            </div>
                            <div class="ty-product-filters__item-more">
                                <ul class="ty-product-filters__variants">
                                    {foreach from=$filter.variants item="variant"}
                                        {$checkbox_id = "lr_homepage_search_filter_`$filter_uid`_`$variant.variant_id`"}
                                        <li class="lr-homepage-search-filters__variant ty-product-filters__group ut2-product-filters__variant">
                                            <input class="cm-lr-homepage-search-filter"
                                                   type="checkbox"
                                                   id="{$checkbox_id}"
                                                   value="{$variant.variant_id}"
                                                   data-ca-filter-id="{$filter.filter_id}">
                                            <label for="{$checkbox_id}">
                                                <span>
                                                    <span class="ut2-product-filters__variant__prefix"></span>
                                                    <span class="ut2-product-filters__variant__value">{$variant.variant|escape}</span>
                                                    <span class="ut2-product-filters__variant__suffix"></span>
                                                </span>
                                            </label>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    {/if}
                </div>

                {if !$products_dropdown_inserted && $filter.key == "category"}
                    {$products_dropdown_inserted = true}
                    <div class="ut2__horizontal-product-filters-dropdown ut2-scroll-item hidden" data-ca-lr-homepage-search-filters-products-dropdown>
                        <div id="sw_elm_filter_{$products_filter_uid}" class="ty-horizontal-product-filters-dropdown__wrapper {if $settings.ab__device !== 'desktop'}cm-abt--ut2-toggle-scroll{/if} cm-combination">{"lr_design_changes.homepage_search_filters_products_title"|__}<span data-ca-lr-homepage-search-filters-products-count class="hidden"></span><i class="ty-horizontal-product-filters-dropdown__icon ty-icon-down-micro"></i></div>
                        {if $settings.ab__device !== "mobile"}
                            <div id="elm_filter_{$products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content lr-homepage-search-filters__products-content cm-horizontal-filters-content cm-smart-position-h">
                                <div type="button" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$products_filter_uid}"><i class="ut2-icon-baseline-close"></i></div>
                                <div id="lr_homepage_search_filters_products_{$filter_data.block_id}"
                                     class="cm-lr-homepage-search-filters-products"
                                     data-ca-default-text='{"lr_design_changes.homepage_search_filters_apply_filters_placeholder"|__}'
                                     data-ca-empty-text='{"lr_design_changes.homepage_search_filters_no_products"|__}'
                                     data-ca-loading-text='{"lr_design_changes.homepage_search_filters_loading_products"|__}'>
                                    <div class="ty-product-filters__item-more">
                                        <ul class="ty-product-filters__variants">
                                            <li class="ty-product-filters__group">
                                                <span>{"lr_design_changes.homepage_search_filters_apply_filters_placeholder"|__}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                {/if}
            {/foreach}

            {if $settings.ab__device === "mobile"}
                {$products_dropdown_inserted = false}
                {capture name="lr_homepage_search_filters_mobile_content"}
                    {foreach from=$filter_data.filters item="filter"}
                        {$filter_uid = "`$filter_data.block_id`_`$filter.filter_id`"}
                        {$is_age_filter = $filter.key == "age"}

                        <div id="elm_filter_{$filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content cm-horizontal-filters-content {if $is_age_filter}lr-homepage-search-filters__age-filter{/if}">
                            <div class="ty-horizontal-product-filters-dropdown__title">
                                <span>{$filter.title|escape}</span>
                                <div type="button" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$filter_uid}"><i class="ut2-icon-baseline-close"></i></div>
                            </div>
                            <div class="ty-product-filters__item-more">
                                <ul class="ty-product-filters__variants">
                                    {foreach from=$filter.variants item="variant"}
                                        {$checkbox_id = "lr_homepage_search_filter_mobile_`$filter_uid`_`$variant.variant_id`"}
                                        <li class="lr-homepage-search-filters__variant ty-product-filters__group ut2-product-filters__variant">
                                            <input class="cm-lr-homepage-search-filter"
                                                   type="checkbox"
                                                   id="{$checkbox_id}"
                                                   value="{$variant.variant_id}"
                                                   data-ca-filter-id="{$filter.filter_id}">
                                            <label for="{$checkbox_id}">
                                                <span>
                                                    <span class="ut2-product-filters__variant__prefix"></span>
                                                    <span class="ut2-product-filters__variant__value">{$variant.variant|escape}</span>
                                                    <span class="ut2-product-filters__variant__suffix"></span>
                                                </span>
                                            </label>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>

                        {if !$products_dropdown_inserted && $filter.key == "category"}
                            {$products_dropdown_inserted = true}
                            <div id="elm_filter_{$products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content lr-homepage-search-filters__products-content cm-horizontal-filters-content">
                                <div class="ty-horizontal-product-filters-dropdown__title">
                                    <span>{"lr_design_changes.homepage_search_filters_products_title"|__}</span>
                                    <div class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$products_filter_uid}"><i class="ut2-icon-baseline-close"></i></div>
                                </div>
                                <div id="lr_homepage_search_filters_products_{$filter_data.block_id}"
                                     class="cm-lr-homepage-search-filters-products"
                                     data-ca-default-text='{"lr_design_changes.homepage_search_filters_apply_filters_placeholder"|__}'
                                     data-ca-empty-text='{"lr_design_changes.homepage_search_filters_no_products"|__}'
                                     data-ca-loading-text='{"lr_design_changes.homepage_search_filters_loading_products"|__}'>
                                    <div class="ty-product-filters__item-more">
                                        <ul class="ty-product-filters__variants">
                                            <li class="ty-product-filters__group">
                                                <span>{"lr_design_changes.homepage_search_filters_apply_filters_placeholder"|__}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                {/capture}
            {/if}
        </div>

        <div class="lr-homepage-search-filters__apply">
            <button type="button"
                    class="ty-btn ty-btn__primary"
                    data-ca-lr-homepage-search-filters-apply>
                {__("lr_design_changes.homepage_search_filters_show_results")}
            </button>
        </div>

        <button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>

        {if $settings.ab__device === "mobile"}
            {$smarty.capture.lr_homepage_search_filters_mobile_content nofilter}
        {/if}

        {include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll|default:2}
    <!--{$id}--></div>
{/if}
