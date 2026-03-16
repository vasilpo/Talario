<!--override module sd_home_filters-->
{** block-description:horizontal_filters **}

{if $block.type == "product_filters" || ($block.type == "product_filters_home" && $block.properties.sd_apply_button == "YesNo::NO"|enum)}
    {script src="js/tygh/product_filters.js"}
{/if}

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
                        <div id="sw_elm_filter_{$sd_home_filters_products_filter_uid}" class="ty-horizontal-product-filters-dropdown__wrapper {if $settings.ab__device !== 'desktop'}cm-abt--ut2-toggle-scroll{/if} cm-combination">{"sd_home_filters.products_filter_title"|__}<i class="ty-horizontal-product-filters-dropdown__icon ty-icon-down-micro"></i></div>
                        {if $settings.ab__device !== 'mobile'}
                            <div id="elm_filter_{$sd_home_filters_products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content custom-content cm-horizontal-filters-content cm-smart-position-h">

                                <div id="sd_home_filters_products_{$block.block_id}"
                                    class="cm-sd-home-filters-products"
                                    data-ca-default-text='{"sd_home_filters.apply_filters_placeholder"|__}'
                                    data-ca-empty-text='{"sd_home_filters.no_products"|__}'
                                    data-ca-loading-text='{"sd_home_filters.loading_products"|__}'
                                    data-ca-view-all-text='{"sd_home_filters.view_all_products"|__}'>
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
                    <div id="sw_elm_filter_{$sd_home_filters_products_filter_uid}" class="ty-horizontal-product-filters-dropdown__wrapper {if $settings.ab__device !== 'desktop'}cm-abt--ut2-toggle-scroll{/if} cm-combination">{"sd_home_filters.products_filter_title"|__}<i class="ty-horizontal-product-filters-dropdown__icon ty-icon-down-micro"></i></div>
                    {if $settings.ab__device !== 'mobile'}
                        <div id="elm_filter_{$sd_home_filters_products_filter_uid}" class="cm-popup-box hidden ty-horizontal-product-filters-dropdown__content custom-content cm-horizontal-filters-content cm-smart-position-h">

                            <div id="sd_home_filters_products_{$block.block_id}"
                                class="cm-sd-home-filters-products"
                                data-ca-default-text='{"sd_home_filters.apply_filters_placeholder"|__}'
                                data-ca-empty-text='{"sd_home_filters.no_products"|__}'
                                data-ca-loading-text='{"sd_home_filters.loading_products"|__}'
                                data-ca-view-all-text='{"sd_home_filters.view_all_products"|__}'>
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
                                    <button type="button" class="ut2-btn-close cm-external-click" data-ca-external-click-id="sw_elm_filter_{$sd_home_filters_products_filter_uid}"><i class="ut2-icon-baseline-close"></i></button>
                                </div>
                                <div id="sd_home_filters_products_{$block.block_id}"
                                    class="cm-sd-home-filters-products"
                                    data-ca-default-text='{"sd_home_filters.apply_filters_placeholder"|__}'
                                    data-ca-empty-text='{"sd_home_filters.no_products"|__}'
                                    data-ca-loading-text='{"sd_home_filters.loading_products"|__}'
                                    data-ca-view-all-text='{"sd_home_filters.view_all_products"|__}'>
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
                                data-ca-loading-text='{"sd_home_filters.loading_products"|__}'
                                data-ca-view-all-text='{"sd_home_filters.view_all_products"|__}'>
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

{if $block.type == "product_filters_home" && $block.properties.sd_apply_button == "YesNo::YES"|enum}
    <script>
        (function(_, $) {
            $(document).ready(function() {
                var $container = $('#product_filters_{$block.block_id}');
                var $search_form = $('form[name="search_form"]').first();
                var filter_selector = '.cm-product-filters-checkbox, .cm-product-filters-select';

                function get_features_hash() {
                    var features = {};

                    $container.find('.cm-product-filters-checkbox:checked, .cm-product-filters-select').each(function() {
                        var filter_id = $(this).data('caFilterId'),
                            variant_id = $(this).val();

                        if (filter_id && variant_id) {
                            features[filter_id] = features[filter_id] || [];
                            $.merge(features[filter_id], $.makeArray(variant_id));
                        }
                    });

                    return $.map(features, function(variants, filter_id) {
                        return filter_id + '-' + variants.join('-');
                    }).join('_');
                }

                function get_search_params() {
                    var search_params = {};
                    var search_query = '';

                    if (!$search_form.length) {
                        return search_params;
                    }

                    // Reuse all active search form params to keep search behavior consistent.
                    $.each($search_form.serializeArray(), function(index, field) {
                        if (!field.name || field.name === 'dispatch' || field.name === 'features_hash') {
                            return;
                        }

                        search_params[field.name] = field.value;
                    });

                    search_query = $.trim(search_params.q || '');

                    if (!search_query.length) {
                        return {};
                    }

                    search_params.q = search_query;
                    search_params.search_performed = 'Y';

                    return search_params;
                }

                function attach_params(url, params) {
                    $.each(params, function(name, value) {
                        if (typeof value === 'undefined' || value === null) {
                            return;
                        }

                        url = $.attachToUrl(url, name + '=' + encodeURIComponent(value));
                    });

                    return url;
                }

                function sync_search_form_features_hash() {
                    var features_hash = get_features_hash();
                    var $features_hash_input = $search_form.find('input[name="features_hash"]');

                    if (!$search_form.length) {
                        return;
                    }

                    // Keep the search form aligned with the currently selected filter state.
                    if (features_hash.length) {
                        if (!$features_hash_input.length) {
                            $features_hash_input = $('<input />', {
                                type: 'hidden',
                                name: 'features_hash'
                            }).appendTo($search_form);
                        }

                        $features_hash_input.val(features_hash);
                    } else {
                        $features_hash_input.remove();
                    }
                }

                $container.off('change.sd_home_filters_sync', filter_selector);
                $container.on('change.sd_home_filters_sync', filter_selector, function() {
                    sync_search_form_features_hash();
                });

                $('#apply_filters_btn_{$block.block_id}').off('click.sd_home_filters_apply').on('click.sd_home_filters_apply', function() {
                    var url = $container.data('caBaseUrl');
                    var features_hash = get_features_hash();
                    var search_params = get_search_params();

                    if (features_hash.length) {
                        url = $.attachToUrl(url, 'features_hash=' + encodeURIComponent(features_hash));
                    } else {
                        url += (url.indexOf('?') === -1 ? '?' : '&') + 'q=&search_performed=Y';
                    }

                    url = attach_params(url, search_params);

                    window.location.href = fn_url(url);
                });

                $search_form.off('submit.sd_home_filters_sync').on('submit.sd_home_filters_sync', function() {
                    sync_search_form_features_hash();
                });

                sync_search_form_features_hash();
            });
            }(Tygh, Tygh.$));
    </script>
{/if}

<script>
    (function (_, $) {
        $.ceEvent('on', 'ce.commoninit', function (context) {
            var block_id = '{$block.block_id|escape:javascript}';
            var $container = $('#product_filters_' + block_id, context);
            var $products_container = $('#sd_home_filters_products_' + block_id, context);
            var request_endpoint = fn_url('sd_home_filters.get_products');
            var filter_selector = '.cm-product-filters-checkbox, .cm-product-filters-select';
            var city_filter_id = '{$sd_home_filters_city_filter_id|escape:javascript}';
            var age_filter_id = '{$sd_home_filters_age_filter_id|escape:javascript}';
            var category_filter_id = '{$sd_home_filters_category_filter_id|escape:javascript}';

            if (!$container.length || !$products_container.length) {
                return;
            }

            function set_placeholder(text) {
                var $list = $('<div/>', {
                    'class': 'sd-home-filters-products-list'
                }).append(
                    $('<span/>', {
                        text: text
                    })
                );

                $products_container
                    .empty()
                    .append(
                        $('<div/>', {
                            'class': 'ty-product-filters__item-more'
                        }).append($list)
                    );
            }

            function render_products(products_response) {
                var $list = $('<div/>', {
                    'class': 'sd-home-filters-products-list'
                });
                var $wrapper = $('<div/>', {
                    'class': 'ty-product-filters__item-more'
                });
                var items = products_response && products_response.items ? products_response.items : [];
                var has_more_items = products_response && products_response.has_more;
                var search_url = products_response && products_response.search_url ? products_response.search_url : '';
                var view_all_text = $products_container.data('caViewAllText');

                $.each(items, function (_, item) {
                    if (!item.item_url || !item.item) {
                        return;
                    }

                    $list.append(
                        $('<a/>', {
                            'class': 'sd-home-filters-products-item',
                            href: item.item_url
                        }).append(
                            $('<span/>', {
                                text: item.item
                            }),
                            $('<span/>', {
                                'class': 'sd-home-filters-products-item-arrow ut2-icon-right-sight'
                            })
                        )
                    );
                });

                $wrapper.append($list);

                if (has_more_items && search_url) {
                    $wrapper.append(
                        $('<div/>', {
                            'class': 'ty-product-filters__tools'
                        }).append(
                            $('<a/>', {
                                'class': 'ty-btn ty-btn__primary',
                                href: search_url,
                                text: view_all_text
                            })
                        )
                    );
                }

                $products_container
                    .empty()
                    .append($wrapper);
            }

            function collect_filter_values(filter_id) {
                var selected_values = [];

                $container.find('.cm-product-filters-checkbox[data-ca-filter-id="' + filter_id + '"]:checked').each(function () {
                    selected_values.push(String($(this).val()));
                });

                $container.find('.cm-product-filters-select[data-ca-filter-id="' + filter_id + '"]').each(function () {
                    var value = $(this).val();

                    if (value === null || value === '' || typeof value === 'undefined') {
                        return;
                    }

                    if ($.isArray(value)) {
                        $.each(value, function (_, current_value) {
                            selected_values.push(String(current_value));
                        });
                    } else {
                        selected_values.push(String(value));
                    }
                });

                return selected_values;
            }

            function update_filter_highlight(filter_id) {
                var $filter_switch = $container.find('#sw_elm_filter_' + block_id + '_' + filter_id);
                var has_selected_values = collect_filter_values(filter_id).length > 0;

                if (!$filter_switch.length) {
                    return;
                }

                $filter_switch.toggleClass('sd-home-filters-highlighted', has_selected_values);
            }

            function update_filters_highlight() {
                $.each([city_filter_id, age_filter_id, category_filter_id], function (_, filter_id) {
                    if (!filter_id) {
                        return;
                    }

                    update_filter_highlight(filter_id);
                });
            }

            function get_features_hash() {
                var selected_filters = {};
                var filter_ids = [city_filter_id, age_filter_id, category_filter_id];

                $.each(filter_ids, function (_, filter_id) {
                    var values = collect_filter_values(filter_id);

                    if (values.length) {
                        selected_filters[filter_id] = values;
                    }
                });

                return $.map(selected_filters, function (variants, filter_id) {
                    return filter_id + '-' + variants.join('-');
                }).join('_');
            }

            function get_category_names() {
                var category_names = [];

                $container.find('.cm-product-filters-checkbox[data-ca-filter-id="' + category_filter_id + '"]:checked').each(function () {
                    var category_name = $.trim($(this).closest('.cm-product-filters-checkbox-container').find('label').text());

                    if (category_name.length) {
                        category_names.push(category_name);
                    }
                });

                $container.find('.cm-product-filters-select[data-ca-filter-id="' + category_filter_id + '"]').each(function () {
                    var value = $(this).val();

                    if (value === null || value === '' || typeof value === 'undefined') {
                        return;
                    }

                    $(this).find('option:selected').each(function () {
                        var category_name = $.trim($(this).text());

                        if (category_name.length) {
                            category_names.push(category_name);
                        }
                    });
                });

                return $.grep(category_names, function (category_name, index) {
                    return $.inArray(category_name, category_names) === index;
                });
            }

            function load_products() {
                var default_text = $products_container.data('caDefaultText');
                var empty_text = $products_container.data('caEmptyText');
                var loading_text = $products_container.data('caLoadingText');
                var features_hash = get_features_hash();
                var category_names = get_category_names();

                if (!city_filter_id || !age_filter_id || !category_filter_id) {
                    set_placeholder(default_text);
                    return;
                }

                if (!features_hash || !category_names.length) {
                    set_placeholder(default_text);
                    return;
                }

                set_placeholder(loading_text);

                $.ceAjax('request', request_endpoint, {
                    method: 'get',
                    hidden: true,
                    caching: false,
                    result_ids: '',
                    data: {
                        features_hash: features_hash,
                        category_names: category_names,
                        category_filter_id: category_filter_id
                    },
                    callback: function (response) {
                        var products = response && response.sd_home_filters_products ? response.sd_home_filters_products : [];

                        if (!products.items || !products.items.length) {
                            set_placeholder(empty_text);
                            return;
                        }

                        render_products(products);
                    }
                });
            }

            $container.off('change.sd_home_products', filter_selector);
            $container.on('change.sd_home_products', filter_selector, function () {
                var filter_id = String($(this).data('caFilterId') || '');

                if (filter_id) {
                    update_filter_highlight(filter_id);
                } else {
                    update_filters_highlight();
                }

                load_products();
            });

            update_filters_highlight();
            load_products();
        });
    }(Tygh, Tygh.$));
</script>
