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

$schema['import_presets'] = array(
    'permissions' => 'manage_catalog',
    'condition' => array(
        'operator' => 'and',
        'function' => array('fn_check_current_user_access', 'exim_access'),
    ),
);

$schema['advanced_import'] = array(
    'permissions' => 'manage_catalog',
    'modes'       => array(
        'import' => array(
            'permissions' => 'manage_catalog',
            'condition' => array(
                'operator' => 'and',
                'function' => array('fn_check_current_user_access', 'exim_access'),
            ),
        ),
    ),
);

return $schema;
