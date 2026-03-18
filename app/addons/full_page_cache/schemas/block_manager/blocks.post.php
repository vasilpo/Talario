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
$schema['my_account']['session_dependent'] = true;
$schema['cart_content']['session_dependent'] = true;
$schema['html_block']['session_dependent'] = true;
$schema['currencies']['session_dependent'] = true;
$schema['languages']['session_dependent'] = true;

if (isset($schema['geo_maps_customer_location'])) {
    $schema['geo_maps_customer_location']['session_dependent'] = true;
}

if (isset($schema['location_selector'])) {
    $schema['location_selector']['session_dependent'] = true;
}

if (isset($schema['closest_vendors'])) {
    $schema['closest_vendors']['session_dependent'] = true;
}

return $schema;
