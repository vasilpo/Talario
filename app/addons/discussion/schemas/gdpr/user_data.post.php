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

$schema['discussions_data'] = array(
    'collect_data_callback' => function ($params) {
        $discussions = array();

        if (isset($params['user_id'])) {
            list($discussions) = fn_get_discussions(array('user_id' => $params['user_id']));
        }

        return $discussions;
    },
    'update_data_callback' => function ($discussions) {
        if (is_array($discussions)) {
            $posts = array();

            foreach ($discussions as $discussion) {

                if (!empty($discussion['post_id'])) {
                    $posts[$discussion['post_id']] = $discussion;
                }
            }

            if ($posts) {
                fn_update_discussion_posts($posts);
            }
        }
    },
    'params'        => array(
        'fields_list' => array('name', 'ip_address'),
    ),
);

return $schema;
