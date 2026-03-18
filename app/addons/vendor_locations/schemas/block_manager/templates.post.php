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

if (!defined('BOOTSTRAP')) { exit('Access denied'); }

$schema['addons/vendor_locations/blocks/closest_vendors.tpl'] = array(
    'settings' => array(
        'number_of_columns' => array(
            'type' => 'input',
            'default_value' => 5,
        ),
        'show_location' => array(
            'type' => 'checkbox',
            'default_value' => 'Y',
        ),
        'show_products_count' => array(
            'type' => 'checkbox',
            'default_value' => 'Y',
        ),
    ),
    'fillings' => array('all', 'manually'),
    'params' => array(
        'status' => 'A',
    ),
);

return $schema;
