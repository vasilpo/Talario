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

$schema['controllers']['discussion'] = [
    'modes' => [
        'add'      => [
            'permissions' => true,
        ],
        /**
         * discussion.view is not used in the administration panel,
         * but this action is required for proper permissions check of vendors
         */
        'view'     => [
            'permissions' => true,
        ],
        'update'   => [
            'permissions' => false,
        ],
        'delete'   => [
            'permissions' => false,
        ],
        'm_delete' => [
            'permissions' => false,
        ],
        /**
         * For add-on Vendor privileges
         */
        'products_and_pages' => [
            'permissions' => true,
        ],
    ],
];

$schema['controllers']['discussion_manager'] = [
    'modes' => [
        'manage' => [
            'permissions' => true,
        ],
    ],
];

$schema['index']['modes']['set_post_status'] = [
    'permissions' => false,
];

$schema['index']['modes']['delete_post'] = [
    'permissions' => false,
];

$schema['tools']['modes']['update_status']['param_permissions']['table']['discussion_posts'] = false;

return $schema;
