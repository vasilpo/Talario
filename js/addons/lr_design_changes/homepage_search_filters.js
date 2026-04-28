(function (_, $) {
    var filter_selector = '.cm-lr-homepage-search-filter';
    var product_selector = '.cm-lr-homepage-search-product';

    function get_config($container) {
        return {
            block_id: String($container.attr('id') || '').replace('lr_homepage_search_filters_', ''),
            category_filter_id: String($container.data('caLrHomepageSearchFiltersCategoryFilterId') || ''),
            products_limit: String($container.data('caLrHomepageSearchFiltersProductsLimit') || ''),
            products_switch_id: String($container.data('caLrHomepageSearchFiltersProductsSwitchId') || ''),
            request_endpoint: String($container.data('caLrHomepageSearchFiltersRequestEndpoint') || ''),
            search_url: String($container.data('caLrHomepageSearchFiltersUrl') || 'products.search')
        };
    }

    function add_filter_value(selected_filters, filter_id, value) {
        if (!filter_id || typeof value === 'undefined' || value === null || value === '') {
            return;
        }

        value = String(value);
        selected_filters[filter_id] = selected_filters[filter_id] || [];

        if ($.inArray(value, selected_filters[filter_id]) === -1) {
            selected_filters[filter_id].push(value);
        }
    }

    function collect_selected_filters($container) {
        var selected_filters = {};

        $container.find(filter_selector).each(function () {
            var $control = $(this);
            var filter_id = String($control.data('caFilterId') || '');
            var value = $control.val();

            if ($control.is(':checkbox') && !$control.prop('checked')) {
                return;
            }

            if ($.isArray(value)) {
                $.each(value, function (_, current_value) {
                    add_filter_value(selected_filters, filter_id, current_value);
                });
            } else {
                add_filter_value(selected_filters, filter_id, value);
            }
        });

        return selected_filters;
    }

    function get_features_hash($container) {
        return $.map(collect_selected_filters($container), function (variants, filter_id) {
            return filter_id + '-' + variants.join('-');
        }).join('_');
    }

    function get_category_names($container, config) {
        var category_names = [];

        $container.find(filter_selector + '[data-ca-filter-id="' + config.category_filter_id + '"]:checked').each(function () {
            var category_name = $.trim($(this).closest('.lr-homepage-search-filters__variant').find('label').text());

            if (category_name.length) {
                category_names.push(category_name);
            }
        });

        return $.grep(category_names, function (category_name, index) {
            return $.inArray(category_name, category_names) === index;
        });
    }

    function get_search_form() {
        var $forms = $('form[name="search_form"]');
        var $visible_forms = $forms.filter(':visible');

        return $visible_forms.length ? $visible_forms.first() : $forms.first();
    }

    function get_search_params($search_form) {
        var search_params = {};
        var search_query = '';

        if (!$search_form.length) {
            return search_params;
        }

        $.each($search_form.serializeArray(), function (index, field) {
            if (
                !field.name
                || field.name === 'dispatch'
                || field.name === 'features_hash'
                || field.name === 'cid'
                || field.name === 'category_id'
                || field.name === 'search_performed'
            ) {
                return;
            }

            search_params[field.name] = field.value;
        });

        search_query = $.trim(search_params.q || '');

        if (!search_query.length) {
            return {};
        }

        search_params.q = search_query;

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

    function get_search_url($container, config) {
        var url = config.search_url;
        var features_hash = get_features_hash($container);

        url = $.attachToUrl(url, 'search_performed=Y');

        if (features_hash.length) {
            return $.attachToUrl(url, 'features_hash=' + encodeURIComponent(features_hash));
        }

        return url;
    }

    function get_products_container(config) {
        return $('#lr_homepage_search_filters_products_' + config.block_id);
    }

    function get_products_dropdown($container) {
        return $container.find('[data-ca-lr-homepage-search-filters-products-dropdown]');
    }

    function get_selected_product_ids($products_container) {
        return $.map($products_container.find(product_selector + ':checked'), function (element) {
            return String($(element).val());
        });
    }

    function get_stored_product_ids($container) {
        return $.makeArray($container.data('caLrHomepageSearchFiltersSelectedProductIds') || []);
    }

    function set_stored_product_ids($container, selected_ids) {
        $container.data('caLrHomepageSearchFiltersSelectedProductIds', selected_ids);
    }

    function set_products_dropdown_visibility($container, is_visible) {
        var $products_dropdown = get_products_dropdown($container);

        if (!$products_dropdown.length) {
            return;
        }

        $products_dropdown.toggleClass('hidden', !is_visible);
        $(window).trigger('resize');
    }

    function update_products_state($container, config, selected_ids) {
        var $products_container = get_products_container(config);
        var current_selected_ids = typeof selected_ids === 'undefined'
            ? get_selected_product_ids($products_container)
            : selected_ids;
        var selected_count = current_selected_ids.length;
        var $switch = $('#' + config.products_switch_id);
        var $selected_count = $switch.find('[data-ca-lr-homepage-search-filters-products-count]');

        if (!$switch.length || !$selected_count.length) {
            return;
        }

        $switch.toggleClass('active selected lr-homepage-search-filters-highlighted', selected_count > 0);
        $selected_count.text(selected_count);
        $selected_count.toggleClass('hidden', selected_count === 0);
        set_stored_product_ids($container, current_selected_ids);
    }

    function set_products_placeholder($container, config, text) {
        var $products_container = get_products_container(config);
        var stored_product_ids = get_stored_product_ids($container);

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

        set_products_dropdown_visibility($container, false);
        update_products_state($container, config, stored_product_ids);
    }

    function render_products($container, config, response) {
        var $products_container = get_products_container(config);
        var $list = $('<ul/>', {
            'class': 'ty-product-filters__variants'
        });
        var $wrapper = $('<div/>', {
            'class': 'ty-product-filters__item-more'
        });
        var items = response && response.items ? response.items : [];
        var selected_product_ids = get_stored_product_ids($container);

        $.each(items, function (index, item) {
            var item_id = item && item.item_id ? String(item.item_id) : '';
            var item_text = item && item.item ? String(item.item) : '';
            var checkbox_id = 'lr_homepage_search_product_' + config.block_id + '_' + index;

            if (!item_id || !item_text.length) {
                return;
            }

            $list.append(
                $('<li/>', {
                    'class': 'lr-homepage-search-filters__variant ty-product-filters__group ut2-product-filters__variant'
                }).append(
                    $('<input/>', {
                        'class': 'cm-lr-homepage-search-product',
                        type: 'checkbox',
                        value: item_id,
                        checked: $.inArray(item_id, selected_product_ids) !== -1,
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

        set_products_dropdown_visibility($container, items.length > 0);
        update_products_state($container, config);
    }

    function load_products($container, config) {
        var $products_container = get_products_container(config);
        var default_text = $products_container.data('caDefaultText');
        var empty_text = $products_container.data('caEmptyText');
        var loading_text = $products_container.data('caLoadingText');
        var features_hash = get_features_hash($container);
        var category_names = get_category_names($container, config);

        if (!config.request_endpoint || !config.category_filter_id) {
            return;
        }

        if (!features_hash || !category_names.length) {
            set_stored_product_ids($container, []);
            set_products_placeholder($container, config, default_text);
            return;
        }

        set_products_placeholder($container, config, loading_text);

        $.ceAjax('request', config.request_endpoint, {
            method: 'get',
            hidden: true,
            caching: false,
            result_ids: '',
            data: {
                features_hash: features_hash,
                category_names: category_names,
                category_filter_id: config.category_filter_id,
                products_dropdown_limit: config.products_limit
            },
            callback: function (response) {
                var products_response = response && response.lr_homepage_search_filters_products
                    ? response.lr_homepage_search_filters_products
                    : {};

                if (!products_response.items || !products_response.items.length) {
                    set_products_placeholder($container, config, empty_text);
                    return;
                }

                render_products($container, config, products_response);
            }
        });
    }

    function update_filter_state($container, filter_id) {
        var selected_count = $container.find(filter_selector + '[data-ca-filter-id="' + filter_id + '"]:checked').length;
        var $switch = $container.find('[data-ca-lr-homepage-search-filters-switch][data-ca-filter-id="' + filter_id + '"]');
        var $selected_count = $switch.find('[data-ca-lr-homepage-search-filters-count]');

        if (!$switch.length || !$selected_count.length) {
            return;
        }

        $switch.toggleClass('active selected lr-homepage-search-filters-highlighted', selected_count > 0);
        $selected_count.text(selected_count);
        $selected_count.toggleClass('hidden', selected_count === 0);
    }

    function update_filters_state($container) {
        $container.find('[data-ca-lr-homepage-search-filters-switch]').each(function () {
            update_filter_state($container, String($(this).data('caFilterId') || ''));
        });
    }

    function go_to_search($container, config, $search_form) {
        var selected_product_ids = get_selected_product_ids(get_products_container(config));
        var search_params = get_search_params($search_form);
        var category_names = get_category_names($container, config);

        if (selected_product_ids.length) {
            $.ceAjax('request', config.request_endpoint, {
                method: 'get',
                hidden: true,
                caching: false,
                result_ids: '',
                data: {
                    features_hash: get_features_hash($container),
                    category_names: category_names,
                    category_filter_id: config.category_filter_id,
                    products_dropdown_limit: config.products_limit,
                    selected_category_ids: selected_product_ids,
                    search_params: search_params
                },
                callback: function (response) {
                    var products_response = response && response.lr_homepage_search_filters_products
                        ? response.lr_homepage_search_filters_products
                        : {};

                    window.location.href = products_response.search_url || attach_params(get_search_url($container, config), search_params);
                }
            });

            return;
        }

        window.location.href = attach_params(get_search_url($container, config), search_params);
    }

    function bind_apply_button($container, config) {
        $container.off('click.lr_homepage_search_filters', '[data-ca-lr-homepage-search-filters-apply]');
        $container.on('click.lr_homepage_search_filters', '[data-ca-lr-homepage-search-filters-apply]', function () {
            go_to_search($container, config, get_search_form());
        });
    }

    function bind_search_form($container, config) {
        var $search_form = get_search_form();

        if (!$search_form.length) {
            return;
        }

        $search_form.off('submit.lr_homepage_search_filters');
        $search_form.on('submit.lr_homepage_search_filters', function (event) {
            event.preventDefault();
            go_to_search($container, config, $(this));
        });
    }

    function init_homepage_search_filters(context) {
        var $context = context ? $(context) : $(_.doc);
        var $containers = $context.find('[data-ca-lr-homepage-search-filters="true"]');

        if ($context.is && $context.is('[data-ca-lr-homepage-search-filters="true"]')) {
            $containers = $containers.add($context);
        }

        $containers.each(function () {
            var $container = $(this);
            var config = get_config($container);

            $container.off('change.lr_homepage_search_filters', filter_selector);
            $container.on('change.lr_homepage_search_filters', filter_selector, function () {
                update_filter_state($container, String($(this).data('caFilterId') || ''));
                load_products($container, config);
            });

            $container.off('change.lr_homepage_search_filters_products', product_selector);
            $container.on('change.lr_homepage_search_filters_products', product_selector, function () {
                update_products_state($container, config);
            });

            bind_apply_button($container, config);
            bind_search_form($container, config);
            update_filters_state($container);
            load_products($container, config);
        });
    }

    $.ceEvent('on', 'ce.commoninit', function (context) {
        init_homepage_search_filters(context);
    });

    $(function () {
        init_homepage_search_filters(_.doc);
    });
}(Tygh, Tygh.$));
