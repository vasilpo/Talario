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

$schema['call_requests'] = array(
    'params'                => array(
        'fields_list' => array('phone', 'name'),
    ),
    'collect_data_callback' => function ($params) {
        $call_requests = array();

        if (!empty($params['email'])) {
            list($call_requests) = fn_get_call_requests(array(
                'order_email' => $params['email'],
            ));
        }

        return $call_requests;
    },
    'update_data_callback' => function ($call_requests) {
        foreach ((array) $call_requests as $request) {
            if (isset($request['request_id'])) {
                fn_update_call_request($request, $request['request_id']);
            }
        }
    },
);

return $schema;
