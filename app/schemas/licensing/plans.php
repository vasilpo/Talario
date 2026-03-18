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

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

//phpcs:ignore
$add_storefront_handler = static function ($default_value) {
    if ($storefronts_limit = fn_get_storage_data('allowed_number_of_stores')) {
        /** @var \Tygh\Storefront\Repository $repository */
        $repository = Tygh::$app['storefront.repository'];
        $storefronts_count = $repository->getCount();

        return $storefronts_count < $storefronts_limit;
    }

    return $default_value;
};

//phpcs:ignore
$addon_full_page_cache_handler = static function () {
    if (fn_get_storage_data('used_full_page_cache_addon')) {
        return true;
    }

    return false;
};

return [];
