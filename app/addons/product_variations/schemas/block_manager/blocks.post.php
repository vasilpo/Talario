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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

require_once (__DIR__ . '/blocks.functions.php');

$schema['products']['content']['items']['fillings']['product_variations.variations_filling'] = [
    'params' => [
        'request' => [
            'variations_by_product_id' => '%PRODUCT_ID%',
        ]
    ]
];
/**
 * @psalm-suppress InvalidArrayOffset
 */
$schema['products']['content']['items']['fillings']['manually']['params']['include_child_variations'] = true;

/**
 * @psalm-suppress InvalidArrayOffset
 */
$schema['products']['content']['items']['fillings']['manually']['params']['group_child_variations'] = false;

$schema['products']['cache']['callable_handlers']['variations_current_product_id'] = [
    'fn_product_variations_blocks_get_current_product_id', ['$block_data']
];
$schema['products']['cache']['update_handlers'][] = 'product_features';

$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'product_variation_group_products';

return $schema;
