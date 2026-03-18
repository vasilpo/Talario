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

/**
 * The "load_products_extra_data" hook handler.
 *
 * Actions performed:
 * - Adds 'unit_name' field in product getting query.
 *
 * @param array<string|array> $extra_fields Extra fields
 * @param array<string|array> $products     List of products
 * @param array<string>       $product_ids  List of product identifiers
 * @param array<string|array> $params       Parameters passed to fn_get_products()
 *
 * @return void
 *
 * @see fn_load_products_extra_data()
 */
function fn_price_per_unit_load_products_extra_data(array &$extra_fields, array $products, array $product_ids, array $params)
{
    if (!in_array('prices', $params['extend'])) {
        return;
    }
    $extra_fields['?:product_descriptions']['fields'][] = 'unit_name';
}
