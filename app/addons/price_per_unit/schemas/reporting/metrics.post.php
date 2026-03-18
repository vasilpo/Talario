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

/** @var array $schema */
$schema['price_per_unit_is_used'] = static function () {
    $products_using_price_per_unit = db_get_field(
        'SELECT COUNT(1) FROM ?:products as products'
        . ' LEFT JOIN ?:product_descriptions as descriptions on descriptions.product_id = products.product_id'
        . ' WHERE descriptions.unit_name != "" AND products.units_in_product != "0.000"'
    );

    return $products_using_price_per_unit > 4;
};

return $schema;
