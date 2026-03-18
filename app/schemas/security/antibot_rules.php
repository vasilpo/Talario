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

$schema = [
    'profiles' => [
        'update' => [
            'request_method' => 'POST',
            'verification_scenario' => 'register',
            'save_post_data' => [
                'user_data',
            ],
            'rewrite_controller_status' => [
                CONTROLLER_STATUS_REDIRECT,
                'profiles.add',
            ],
        ],
    ],

    'orders' => [
        'track_request' => [
            'request_method' => 'POST',
            'verification_scenario' => 'track_orders',
            'terminate_process' => true,
        ],
    ],

    'auth' => [
        'login' => [
            'request_method' => 'POST',
            'verification_scenario' => 'login',
            'save_post_data' => [
                'user_login',
            ],
            'rewrite_controller_status' => [
                CONTROLLER_STATUS_REDIRECT,
            ],
        ],
    ],

    'checkout' => [
        'add_profile' => [
            'request_method'            => 'POST',
            'verification_scenario'     => 'register',
            'save_post_data'            => [
                'user_data',
            ],
            'rewrite_controller_status' => [
                CONTROLLER_STATUS_REDIRECT,
                'checkout.checkout?login_type=register',
            ],
        ],
        'place_order' => [
            'request_method'            => 'POST',
            'verification_scenario'     => 'checkout',
            'save_post_data'            => [
                'user_data',
            ],
            'rewrite_controller_status' => [
                CONTROLLER_STATUS_REDIRECT,
                'checkout.checkout?login_type=guest',
            ],
        ]
    ],
];

return $schema;
