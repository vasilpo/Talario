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

use Tygh\Enum\Addons\AdvancedImport\RelatedObjectTypes;

$schema = array(
    'products' => array(
        RelatedObjectTypes::FEATURE => array(
            'description'        => 'features',
            'items_function'     => 'fn_advanced_import_get_product_features_list',
            'aggregate_field'    => 'Advanced Import: Features',
            'aggregate_function' => 'fn_advanced_import_aggregate_features',
        ),
    ),
);

return $schema;