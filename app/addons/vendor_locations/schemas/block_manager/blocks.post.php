<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

use Tygh\Registry;

require_once Registry::get('config.dir.addons') . '/vendor_locations/schemas/block_manager/blocks.functions.php';

$schema['location_selector'] = array(
    'templates' => 'addons/vendor_locations/blocks/location_selector.tpl',
    'wrappers' => 'blocks/wrappers',
);

$schema['search_vendors'] = array(
    'templates' => 'addons/vendor_locations/blocks/companies_catalog/search_vendors_by_address.tpl',
    'wrappers' => 'blocks/wrappers',
    'show_on_locations' => array(
        'vendors' => 'companies.catalog',
    ),
);

$schema['vendors_map'] = array(
    'templates' => 'addons/vendor_locations/blocks/companies_catalog/vendors_map.tpl',
    'wrappers' => 'blocks/wrappers',
    'show_on_locations' => array(
        'vendors' => 'companies.catalog',
    ),
    'content' => array(
        'items' => array(
            'type' => 'enum',
            'object' => 'vendors',
            'remove_indent' => true,
            'hide_label' => true,
            'items_function' => 'fn_vendor_locations_get_block_vendors',
            'fillings' => array(
                'all' => array(
                    'params' => array(
                        'request' => array(
                            'location_filter' => '%LOCATION_FILTER%',
                            'page' => '%PAGE%',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'cache' => false,
);

$schema['closest_vendors'] = array(
    'content' => array(
        'items' => array(
            'type' => 'enum',
            'object' => 'vendors',
            'remove_indent' => true,
            'hide_label' => true,
            'items_function' => 'fn_vendor_locations_block_get_closest_vendors',
            'fillings' => array(
                'all' => array(),
                'manually' => array(
                    'picker' => 'pickers/companies/picker.tpl',
                    'picker_params' => array(
                        'multiple' => true,
                    ),
                )
            ),
        ),
    ),
    'settings' => array(
        'displayed_vendors' => array(
            'type' => 'input',
            'default_value' => '10'
        ),
    ),
    'templates' => 'addons/vendor_locations/blocks/closest_vendors.tpl',
    'wrappers' => 'blocks/wrappers',
    'cache' => false,
    'brief_info_function' => 'fn_block_get_vendors_info'
);

return $schema;
