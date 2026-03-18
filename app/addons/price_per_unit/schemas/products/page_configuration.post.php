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

$schema['detailed']['sections']['price_per_unit'] = [
    'is_optional' => true,
    'title'       => 'price_per_unit',
    'position'    => 350,
    'fields'      => [
        'unit_name'              => ['is_optional' => true, 'title' => 'unit_name', 'position' => 100],
        'units_in_product'       => ['is_optional' => true, 'title' => 'units_in_product', 'position' => 200],
        'show_price_per_x_units' => ['is_optional' => true, 'title' => 'show_price_per_x_units', 'position' => 300],
    ],
];

return $schema;
