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

defined('BOOTSTRAP') or die('Access denied');

function fn_settings_variants_addons_vendor_data_premoderation_product_premoderation_fields()
{
    $variants = [
        'product_prices:*'    => __('price'),
        'products:list_price' => __('list_price'),
        'products:amount'     => __('quantity'),
    ];

    /**
     * Executes when getting variants of the "Require approval for updates of" setting of Product for
     * the Vendor data premoderation add-on, allows you to add new variants or modify the existing ones
     *
     * @param string[] $variants Setting variants
     */
    fn_set_hook('settings_variants_addons_vendor_data_premoderation_product_premoderation_fields_post', $variants);

    return $variants;
}
