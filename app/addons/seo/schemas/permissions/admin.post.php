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

$schema['seo_rules'] = array (
    'modes' => array (
        'delete' => array (
            'permissions' => 'manage_seo_rules'
        ),
        'manage' => array(
            'vendor_only' => true,
            'use_company' => true,
            'page_title' => 'seo_rules',
            'permissions' => 'view_seo_rules'
        ),
        'update' => array(
            'use_company' => true,
        ),
        'm_update' => array(
            'use_company' => true,
        ),
    ),
    'permissions' => array ('GET' => 'view_seo_rules', 'POST' => 'manage_seo_rules')
);

$schema['seo_redirects'] = array (
    'modes' => array (
        'delete' => array (
            'permissions' => 'manage_seo_rules'
        ),
        'manage' => array(
            'vendor_only' => true,
            'use_company' => true,
            'page_title' => 'seo.redirects_manager',
        ),
        'update' => array(
            'use_company' => true,
        ),
        'm_update' => array(
            'use_company' => true,
        ),
    ),
    'permissions' => array ('GET' => 'view_seo_rules', 'POST' => 'manage_seo_rules')
);

return $schema;
