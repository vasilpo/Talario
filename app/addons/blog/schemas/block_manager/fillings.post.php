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

$schema['blog.recent_posts_scroller'] = array(
    'parent_page_id' => array (
        'type' => 'picker',
        'default_value' => '0',
        'picker' => 'pickers/pages/picker.tpl',
        'picker_params' => array (
            'multiple' => false,
            'use_keys' => 'N',
            'default_name' => __('root_level'),
            'extra_url' => "&page_type=" . PAGE_TYPE_BLOG
        ),
    ),
    'period' => array (
        'type' => 'selectbox',
        'values' => array (
            'A' => 'any_date',
            'D' => 'today',
            'HC' => 'last_days',
        ),
        'default_value' => 'any_date'
    ),
);

$schema['blog.recent_posts'] = array(
    'period' => array (
        'type' => 'selectbox',
        'values' => array (
            'A' => 'any_date',
            'D' => 'today',
            'HC' => 'last_days',
        ),
        'default_value' => 'any_date'
    ),
    'last_days' => array (
        'type' => 'input',
        'default_value' => 1
    ),
    'limit' => array (
        'type' => 'input',
        'default_value' => 3
    ),
);

return $schema;
