(function (_, $) {
    // Shared selector for the built-in horizontal filter controls.
    var filter_selector = '.cm-product-filters-checkbox, .cm-product-filters-select';

    // Read block-specific runtime configuration from the template data attributes.
    function get_config($container) {
        return {
            block_id: String($container.data('caHomeFiltersBlockId') || ''),
            request_endpoint: String($container.data('caHomeFiltersRequestEndpoint') || ''),
            city_filter_id: String($container.data('caHomeFiltersCityFilterId') || ''),
            age_filter_id: String($container.data('caHomeFiltersAgeFilterId') || ''),
            category_filter_id: String($container.data('caHomeFiltersCategoryFilterId') || ''),
            products_switch_id: String($container.data('caHomeFiltersProductsSwitchId') || ''),
            apply_button_id: String($container.data('caHomeFiltersApplyButtonId') || '')
        };
    }

    // Filter state block: collect selected values and build the features hash.
    function get_filter_ids(config) {
        return [config.city_filter_id, config.age_filter_id, config.category_filter_id];
    }

    function collect_filter_values($container, filter_id) {
        var selected_values = [];

        if (!filter_id) {
            return selected_values;
        }

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

    function get_features_hash($container, config) {
        var selected_filters = {};

        $.each(get_filter_ids(config), function (_, filter_id) {
            var values = collect_filter_values($container, filter_id);

            if (values.length) {
                selected_filters[filter_id] = values;
            }
        });

        return $.map(selected_filters, function (variants, filter_id) {
            return filter_id + '-' + variants.join('-');
        }).join('_');
    }

    // Resolve selected category labels because the backend matches parent categories by name.
    function get_category_names($container, config) {
        var category_names = [];

        $container.find('.cm-product-filters-checkbox[data-ca-filter-id="' + config.category_filter_id + '"]:checked').each(function () {
            var category_name = $.trim($(this).closest('.cm-product-filters-checkbox-container').find('label').text());

            if (category_name.length) {
                category_names.push(category_name);
            }
        });

        $container.find('.cm-product-filters-select[data-ca-filter-id="' + config.category_filter_id + '"]').each(function () {
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

    // DOM helpers for the custom subcategory dropdown and the search form.
    function get_products_container($container, config) {
        return $('#sd_home_filters_products_' + config.block_id);
    }

    function get_search_form() {
        return $('form[name="search_form"]').first();
    }

    // Search sync block: preserve the storefront search request alongside filter selections.
    function get_search_params($search_form) {
        var search_params = {};
        var search_query = '';

        if (!$search_form.length) {
            return search_params;
        }

        // Keep the search request aligned with the currently visible search form.
        $.each($search_form.serializeArray(), function (index, field) {
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
        $.each(params, function (name, value) {
            if (typeof value === 'undefined' || value === null) {
                return;
            }

            url = $.attachToUrl(url, name + '=' + encodeURIComponent(value));
        });

        return url;
    }

    // Store the selected subcategories in memory so they survive dropdown re-rendering.
    function get_selected_subcategory_ids($container) {
        return $.map($container.find('.cm-sd-home-filters-subcategory-checkbox:checked'), function (element) {
            return String($(element).val());
        });
    }

    function get_stored_subcategory_ids($container) {
        return $.makeArray($container.data('caSdHomeFiltersSelectedSubcategoryIds') || []);
    }

    function set_stored_subcategory_ids($container, selected_ids) {
        $container.data('caSdHomeFiltersSelectedSubcategoryIds', selected_ids);
    }

    // Highlight the built-in filter switches when they have selected values.
    function update_filter_highlight($container, block_id, filter_id) {
        var $filter_switch = $container.find('#sw_elm_filter_' + block_id + '_' + filter_id);
        var has_selected_values = collect_filter_values($container, filter_id).length > 0;

        if (!$filter_switch.length) {
            return;
        }

        $filter_switch.toggleClass('sd-home-filters-highlighted', has_selected_values);
    }

    function update_filters_highlight($container, config) {
        $.each(get_filter_ids(config), function (_, filter_id) {
            update_filter_highlight($container, config.block_id, filter_id);
        });
    }

    // Subcategory filter block: keep the custom "Lessons" dropdown state in sync with selections.
    function update_subcategory_state($container, config, selected_ids) {
        var $products_container = get_products_container($container, config);
        var current_selected_ids = typeof selected_ids === 'undefined'
            ? get_selected_subcategory_ids($products_container)
            : selected_ids;
        var selected_count = current_selected_ids.length;
        var $filter_switch = $container.find('#' + config.products_switch_id);
        var $selected_count = $filter_switch.find('[data-ca-subcategory-count]');

        if (!$filter_switch.length || !$selected_count.length) {
            return;
        }

        $filter_switch.toggleClass('active selected sd-home-filters-highlighted', selected_count > 0);
        $selected_count.text(selected_count);
        $selected_count.toggleClass('hidden', selected_count === 0);
        set_stored_subcategory_ids($container, current_selected_ids);
    }

    // Render the placeholder for the custom dropdown when subcategories are not available yet.
    function set_placeholder($container, config, text) {
        var $products_container = get_products_container($container, config);
        var stored_subcategory_ids = get_stored_subcategory_ids($container);

        $products_container
            .empty()
            .append(
                $('<div/>', {
                    'class': 'ty-product-filters__item-more'
                }).append(
                    $('<ul/>', {
                        'class': 'ty-product-filters__variants'
                    }).append(
                        $('<li/>', {
                            'class': 'ty-product-filters__group'
                        }).append(
                            $('<span/>', {
                                text: text
                            })
                        )
                    )
                )
            );

        update_subcategory_state($container, config, stored_subcategory_ids);
    }

    // Render subcategories as checkbox variants that mimic the standard filter UI.
    function render_subcategories($container, config, response) {
        var $products_container = get_products_container($container, config);
        var $list = $('<ul/>', {
            'class': 'ty-product-filters__variants'
        });
        var $wrapper = $('<div/>', {
            'class': 'ty-product-filters__item-more'
        });
        var items = response && response.items ? response.items : [];
        var selected_subcategory_ids = get_stored_subcategory_ids($container);

        $.each(items, function (index, item) {
            var item_id = item && item.item_id ? String(item.item_id) : '';
            var item_text = item && item.item ? String(item.item) : '';
            var checkbox_id = 'sd_home_filters_subcategory_' + config.block_id + '_' + index;

            if (!item_id || !item_text.length) {
                return;
            }

            $list.append(
                $('<li/>', {
                    'class': 'cm-product-filters-checkbox-container ty-product-filters__group ut2-product-filters__variant'
                }).append(
                    $('<input/>', {
                        'class': 'cm-sd-home-filters-subcategory-checkbox',
                        type: 'checkbox',
                        value: item_id,
                        checked: $.inArray(item_id, selected_subcategory_ids) !== -1,
                        id: checkbox_id
                    }),
                    $('<label/>', {
                        'for': checkbox_id
                    }).append(
                        $('<span/>').append(
                            $('<span/>', {
                                'class': 'ut2-product-filters__variant__prefix'
                            }),
                            $('<span/>', {
                                'class': 'ut2-product-filters__variant__value',
                                text: item_text
                            }),
                            $('<span/>', {
                                'class': 'ut2-product-filters__variant__suffix'
                            })
                        )
                    )
                )
            );
        });

        $wrapper.append($list);
        $products_container.empty().append($wrapper);

        update_subcategory_state($container, config);
    }

    // Mirror the current feature selection into the main search form before submission.
    function sync_search_form_features_hash($container, config, $search_form) {
        var features_hash = get_features_hash($container, config);
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

    // Request subcategories for the currently selected parent categories.
    function load_subcategories($container, config) {
        var $products_container = get_products_container($container, config);
        var default_text = $products_container.data('caDefaultText');
        var empty_text = $products_container.data('caEmptyText');
        var loading_text = $products_container.data('caLoadingText');
        var features_hash = get_features_hash($container, config);
        var category_names = get_category_names($container, config);

        if (!config.city_filter_id || !config.age_filter_id || !config.category_filter_id) {
            set_stored_subcategory_ids($container, []);
            set_placeholder($container, config, default_text);
            return;
        }

        if (!features_hash || !category_names.length) {
            set_stored_subcategory_ids($container, []);
            set_placeholder($container, config, default_text);
            return;
        }

        set_placeholder($container, config, loading_text);

        $.ceAjax('request', config.request_endpoint, {
            method: 'get',
            hidden: true,
            caching: false,
            result_ids: '',
            data: {
                features_hash: features_hash,
                category_names: category_names,
                category_filter_id: config.category_filter_id
            },
            callback: function (response) {
                var products_response = response && response.sd_home_filters_products ? response.sd_home_filters_products : {};

                if (!products_response.items || !products_response.items.length) {
                    set_placeholder($container, config, empty_text);
                    return;
                }

                render_subcategories($container, config, products_response);
            }
        });
    }

    // Bind base filter events and keep visual highlights in sync.
    function init_filter_state($container, config) {
        $container.off('change.sd_home_products', filter_selector);
        $container.on('change.sd_home_products', filter_selector, function () {
            var filter_id = String($(this).data('caFilterId') || '');

            if (filter_id) {
                update_filter_highlight($container, config.block_id, filter_id);
            } else {
                update_filters_highlight($container, config);
            }

            load_subcategories($container, config);
        });

        $container.off('change.sd_home_products_subcategories', '.cm-sd-home-filters-subcategory-checkbox');
        $container.on('change.sd_home_products_subcategories', '.cm-sd-home-filters-subcategory-checkbox', function () {
            update_subcategory_state($container, config);
        });

        update_filters_highlight($container, config);
    }

    // Bind search-form synchronization and the apply-button navigation flow.
    function init_search_sync($container, config) {
        var $search_form = get_search_form();
        var $apply_button;

        if (!config.apply_button_id) {
            return;
        }

        $apply_button = $('#' + config.apply_button_id);

        if (!$apply_button.length) {
            return;
        }

        $container.off('change.sd_home_filters_sync', filter_selector);
        $container.on('change.sd_home_filters_sync', filter_selector, function () {
            sync_search_form_features_hash($container, config, $search_form);
        });

        $search_form.off('submit.sd_home_filters_sync').on('submit.sd_home_filters_sync', function () {
            sync_search_form_features_hash($container, config, $search_form);
        });

        $apply_button.off('click.sd_home_filters_apply').on('click.sd_home_filters_apply', function () {
            var url = $container.data('caBaseUrl');
            var features_hash = get_features_hash($container, config);
            var search_params = get_search_params($search_form);
            var selected_subcategory_ids = get_selected_subcategory_ids(get_products_container($container, config));
            var category_names = get_category_names($container, config);

            if (selected_subcategory_ids.length) {
                $.ceAjax('request', config.request_endpoint, {
                    method: 'get',
                    hidden: true,
                    caching: false,
                    result_ids: '',
                    data: {
                        features_hash: features_hash,
                        category_names: category_names,
                        category_filter_id: config.category_filter_id,
                        selected_category_ids: selected_subcategory_ids,
                        search_params: search_params
                    },
                    callback: function (response) {
                        var products_response = response && response.sd_home_filters_products ? response.sd_home_filters_products : {};

                        if (products_response.search_url) {
                            window.location.href = products_response.search_url;
                        }
                    }
                });

                return;
            }

            if (features_hash.length) {
                url = $.attachToUrl(url, 'features_hash=' + encodeURIComponent(features_hash));
            } else {
                url += (url.indexOf('?') === -1 ? '?' : '&') + 'q=&search_performed=Y';
            }

            url = attach_params(url, search_params);
            window.location.href = fn_url(url);
        });

        sync_search_form_features_hash($container, config, $search_form);
    }

    // Trigger the initial subcategory load after the block has been initialized.
    function init_subcategory_filter($container, config) {
        load_subcategories($container, config);
    }

    // Initialize every horizontal home-filters block after CS-Cart common init.
    $.ceEvent('on', 'ce.commoninit', function (context) {
        $('.ty-horizontal-product-filters[data-ca-home-filters-block-id]', context).each(function () {
            var $container = $(this);
            var config = get_config($container);

            if (!config.block_id || !config.request_endpoint) {
                return;
            }

            init_filter_state($container, config);
            init_search_sync($container, config);
            init_subcategory_filter($container, config);
        });
    });
}(Tygh, Tygh.$));
