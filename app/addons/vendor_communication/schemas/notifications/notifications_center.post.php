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

use Tygh\NotificationsCenter\NotificationsCenter;

defined('BOOTSTRAP') or die('Access denied');

$schema[NotificationsCenter::SECTION_COMMUNICATION] = [
    'section'      => NotificationsCenter::SECTION_COMMUNICATION,
    'section_name' => __('notifications_center.section.communication'),
    'tags'         => [
        NotificationsCenter::TAG_MESSAGES => [
            'tag'      => NotificationsCenter::TAG_MESSAGES,
            'tag_name' => __('notifications_center.tag.messages'),
        ],
    ],
];

return $schema;
