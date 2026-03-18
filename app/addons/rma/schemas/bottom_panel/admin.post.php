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

use Tygh\Tools\Url;

$schema['rma.returns'] = [
    'from' => [
        'dispatch' => 'rma.returns',
    ],
    'to_customer' => [
        'dispatch' => 'rma.returns'
    ]
];

$schema['rma.details'] = [
    'from' => [
        'dispatch' => 'rma.details',
        'return_id'
    ],
    'to_customer' => function (Url $url) {
        $return_id = (int) $url->getQueryParam('return_id');

        $return_info = fn_get_return_info($return_id);
        $auth = Tygh::$app['session']['auth'];

        if (!empty($return_info) && $return_info['user_id'] == $auth['user_id'] && fn_is_order_allowed($return_info['order_id'], $auth)) {
            return [
                'dispatch' => 'rma.details',
                'return_id' => '%return_id%'
            ];
        } else {
            return [
                'dispatch'  => 'rma.returns'
            ];
        }
    }
];

return $schema;