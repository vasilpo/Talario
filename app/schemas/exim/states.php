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

return [
    'section' => 'states',
    'pattern_id' => 'states',
    'name' => __('states'),
    'key' => ['state_id'],
    'order' => 1,
    'table' => 'states',
    'permissions' => [
        'import' => 'manage_locations',
        'export' => 'view_locations',
    ],
    'references' => [
        'state_descriptions' => [
            'reference_fields' => ['state_id' => '#key', 'lang_code' => '#lang_code'],
            'join_type' => 'LEFT'
        ],
    ],
    'options' => [
        'lang_code' => [
            'title' => 'language',
            'type' => 'languages',
            'default_value' => [DEFAULT_LANGUAGE],
        ],
    ],
    'export_fields' => [
        'State' => [
            'db_field' => 'state',
            'table' => 'state_descriptions',
            'required' => true,
            'multilang' => true,
        ],
        'Language' => [
            'table' => 'state_descriptions',
            'db_field' => 'lang_code',
            'type' => 'languages',
            'required' => true,
            'multilang' => true
        ],
        'Code' => [
            'db_field' => 'code',
            'required' => true,
            'alt_key' => true,
        ],
        'Country code' => [
            'db_field' => 'country_code',
            'required' => true,
            'alt_key' => true,
        ],
    ],
];
