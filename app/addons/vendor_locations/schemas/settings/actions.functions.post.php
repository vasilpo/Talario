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

use Tygh\Addons\VendorLocations\Enum\FilterTypes;

/**
 * Disables filters on disable add-on
 *
 * @param string $status
 */
function fn_settings_actions_addons_post_vendor_locations($status)
{
    if ($status !== 'D') {
        return;
    }

    list($filters) = fn_get_product_filters(array('field_type' => FilterTypes::all()));

    foreach ($filters as $filter) {
        if ($filter['status'] === 'D') {
            continue;
        }
        fn_tools_update_status(array(
            'id' => $filter['filter_id'],
            'id_name' => 'filter_id',
            'status' => 'D',
            'table' => 'product_filters',
        ));
    }
}
