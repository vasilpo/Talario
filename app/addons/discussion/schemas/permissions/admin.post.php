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

use Tygh\Enum\Addons\Discussion\DiscussionObjectTypes;

defined('BOOTSTRAP') or die('Access denied');

$schema['discussion'] = [
    'modes'       => [
        'add'      => [
            'permissions' => 'manage_discussions',
        ],
        /**
         * discussion.view is not used in the administration panel,
         * but this action is required for proper permissions check of vendors
         */
        'view'     => [
            'permissions' => 'view_discussions',
        ],
        'update'   => [
            'param_permissions' => [
                'discussion_type' => [
                    DiscussionObjectTypes::TESTIMONIALS_AND_LAYOUT => 'view_discussions',
                ],
            ],
        ],
        'delete'   => [
            'permissions' => 'manage_discussions',
        ],
        'm_delete' => [
            'permissions' => 'manage_discussions',
        ],
    ],
    'permissions' => 'manage_discussions',
];

$schema['discussion_manager'] = [
    'modes' => [
        'manage' => [
            'permissions' => 'view_discussions',
        ],
    ],
];

$schema['index']['modes']['set_post_status'] = [
    'permissions' => 'manage_discussions',
];

$schema['index']['modes']['delete_post'] = [
    'permissions' => 'manage_discussions',
];

$schema['tools']['modes']['update_status']['param_permissions']['table']['discussion_posts'] = 'manage_discussions';

return $schema;
