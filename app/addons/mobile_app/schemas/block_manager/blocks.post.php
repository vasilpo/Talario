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
$schema['categories']['settings']['category_appearance'] = [
    'type' => 'selectbox',
    'values' => [
        'with_background' => 'mobile_app.category_image_with_background',
        'without_background' => 'mobile_app.category_image_without_background',
        'without_image' => 'mobile_app.category_without_image',
    ],
    'default_value' => 'without_image',
    'tooltip' => __('mobile_app.category_appearance_tooltip')
];

return $schema;
