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
use Tygh\Enum\Addons\Discussion\DiscussionTypes;

$schema['discussion.update'] = [
    'from' => [
        'dispatch'        => 'discussion.update',
        'discussion_type' => DiscussionObjectTypes::TESTIMONIALS_AND_LAYOUT
    ],
    'to_customer' => function () {
        $thread_id = fn_get_discussion(0, DiscussionObjectTypes::TESTIMONIALS_AND_LAYOUT);

        if (!empty($thread_id['thread_id']) && ($thread_id['type'] != DiscussionTypes::TYPE_DISABLED)){
            return [
                'dispatch' => 'discussion.view',
                'thread_id' => $thread_id['thread_id']
            ];
        } else {
            return false;
        }
    }
];

return $schema;