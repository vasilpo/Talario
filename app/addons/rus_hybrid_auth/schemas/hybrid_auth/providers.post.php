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

use Tygh\Registry;

$schema['vkontakte'] = [
    'provider' => 'Vkontakte',
    'callback' => fn_url('/auth/vkontakte'),
    'keys' => [
        'id' => [
            'db_field' => 'app_id',
            'type' => 'input',
            'label' => 'id',
            'required' => true
        ],
        'secret' => [
            'db_field' => 'app_secret_key',
            'type' => 'input',
            'label' => 'secret_key',
            'required' => true
        ]
    ],
    'params' => [
        'vkontakte_callback' => [
            'type' => 'template',
            'template' => 'addons/hybrid_auth/components/callback_url.tpl',
            'callback_url' => '/auth/vkontakte',
        ]
    ],
    'adapter' => 'Tygh\Addons\RusHybridAuth\Providers\Vkontakte',
    'instruction' => 'rus_hybrid_auth.instruction_vkontakte'
];

$schema['mailru'] = [
    'provider' => 'Mailru',
    'keys' => [
        'id' => [
            'db_field' => 'app_id',
            'type' => 'input',
            'label' => 'client_id',
            'required' => true
        ],
        'secret' => [
            'db_field' => 'app_secret_key',
            'type' => 'input',
            'label' => 'client_secret',
            'required' => true
        ]
    ],
    'params' => [
        'mailru_callback' => [
            'type' => 'template',
            'template' => 'addons/hybrid_auth/components/callback_url.tpl',
        ]
    ],
    'adapter' => 'Tygh\Addons\RusHybridAuth\Providers\Mailru',
    'instruction' => 'rus_hybrid_auth.instruction_mailru_new'
];

$schema['yandex'] = [
    'provider' => 'Yandex',
    'callback' => fn_url('auth.process?hauth_done=Yandex'), // For backward compatibility
    'keys' => [
        'id' => [
            'db_field' => 'app_id',
            'type' => 'input',
            'label' => 'id',
            'required' => true
        ],
        'secret' => [
            'db_field' => 'app_secret_key',
            'type' => 'input',
            'label' => 'secret_key',
            'required' => true
        ],
    ],
    'params' => [
        'yandex_callback' => [
            'type' => 'template',
            'template' => 'addons/hybrid_auth/components/callback_url.tpl',
            'callback_url' => '/' . Registry::get('config.customer_index') . '?dispatch=auth.process&hauth_done=Yandex',
        ]
    ],
    'adapter' => 'Tygh\Addons\RusHybridAuth\Providers\Yandex',
    'instruction' => 'rus_hybrid_auth.instruction_yandex'
];

$schema['odnoklassniki'] = [
    'provider' => 'Odnoklassniki',
    'callback' => fn_url('auth.process?hauth_done=Odnoklassniki'), // For backward compatibility
    'keys' => [
        'id' => [
            'db_field' => 'app_id',
            'type' => 'input',
            'label' => 'id',
            'required' => true
        ],
        'key' => [
            'db_field' => 'app_public_key',
            'type' => 'input',
            'label' => 'public_key',
            'required' => true
        ],
        'secret' => [
            'db_field' => 'app_secret_key',
            'type' => 'input',
            'label' => 'secret_key',
            'required' => true
        ],
    ],
    'params' => [
        'odnoklassniki_callback' => [
            'type' => 'template',
            'template' => 'addons/hybrid_auth/components/callback_url.tpl',
            'callback_url' => '/' . Registry::get('config.customer_index') . '?dispatch=auth.process&hauth_done=Odnoklassniki',
        ]
    ],
    'adapter' => 'Tygh\Addons\RusHybridAuth\Providers\Odnoklassniki',
    'instruction' => 'rus_hybrid_auth.instruction_odnoklassniki'
];

return $schema;
