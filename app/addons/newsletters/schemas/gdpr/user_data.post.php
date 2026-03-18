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

$schema['newsletters'] = array(
    'params'                => array(
        'fields_list' => array('email'),
    ),
    'collect_data_callback' => function ($params) {
        if (!empty($params['email'])) {
            list($subscribers) = (array) fn_get_subscribers(array('email' => $params['email']));
            return current($subscribers);
        }
    },
    'update_data_callback' => function ($subscriber) {
        if (isset($subscriber['subscriber_id'])) {
            fn_delete_subscribers(array($subscriber['subscriber_id']));
        }
    },
);

return $schema;

