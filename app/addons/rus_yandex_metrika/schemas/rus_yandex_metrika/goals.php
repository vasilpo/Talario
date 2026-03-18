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

$schema = [
    'engaged_visitor'                => [
        'name'       => __('yandex_metrika_engaged_visitor_text'),
        'type'       => 'number',
        'class'      => 1,
        'depth'      => 5,
        'conditions' => [],
        'flag'       => '',
    ],
    'basket'                         => [
        'name'       => __('yandex_metrika_basket_text'),
        'type'       => 'action',
        'class'      => 1,
        'flag'       => 'basket',
        'depth'      => 0,
        'conditions' => [
            [
                'url'  => 'basket',
                'type' => 'exact',
            ],
        ],
    ],
    'order'                          => [
        'name'       => __('yandex_metrika_order_text'),
        'type'       => 'action',
        'class'      => 1,
        'flag'       => 'order',
        'depth'      => 0,
        'conditions' => [
            [
                'url'  => 'order',
                'type' => 'exact',
            ],
        ],
        'controller' => 'checkout',
        'mode'       => 'complete',
    ],
    'wishlist'                       => [
        'name'       => __('yandex_metrika_wishlist_text'),
        'type'       => 'action',
        'class'      => 1,
        'flag'       => '',
        'depth'      => 0,
        'conditions' => [
            [
                'url'  => 'wishlist',
                'type' => 'exact',
            ],
        ],
    ],
    'buy_with_one_click_form_opened' => [
        'name'       => __('yandex_metrika_buy_with_one_click_form_opened_text'),
        'type'       => 'action',
        'class'      => 1,
        'flag'       => '',
        'depth'      => 0,
        'conditions' => [
            [
                'url'  => 'buy_with_one_click_form_opened',
                'type' => 'exact',
            ],
        ],
    ],
    'call_request'                   => [
        'name'       => __('yandex_metrika_call_request_text'),
        'type'       => 'action',
        'class'      => 1,
        'flag'       => '',
        'depth'      => 0,
        'conditions' => [
            [
                'url'  => 'call_request',
                'type' => 'exact',
            ],
        ],
    ],
];

return $schema;
