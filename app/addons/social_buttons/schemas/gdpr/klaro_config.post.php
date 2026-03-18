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

use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
if (YesNo::isTrue(Registry::get('addons.social_buttons.facebook_enable'))) {
    $schema['services']['facebook'] = [
        'purposes' => ['functional'],
        'name' => 'facebook',
        'translations' => [
            'zz' => [
                'title' => 'social_buttons.facebook_cookie_title',
                'description' => 'social_buttons.facebook_cookie_description'
            ],
        ],
    ];
}

if (YesNo::isTrue(Registry::get('addons.social_buttons.pinterest_enable'))) {
    $schema['services']['pinterest'] = [
        'purposes' => ['functional'],
        'name' => 'pinterest',
        'translations' => [
            'zz' => [
                'title' => 'social_buttons.pinterest_cookie_title',
                'description' => 'social_buttons.pinterest_cookie_description'
            ],
        ],
    ];
}

if (YesNo::isTrue(Registry::get('addons.social_buttons.twitter_enable'))) {
    $schema['services']['twitter'] = [
        'purposes' => ['functional'],
        'name' => 'twitter',
        'translations' => [
            'zz' => [
                'title' => 'social_buttons.twitter_cookie_title',
                'description' => 'social_buttons.twitter_cookie_description'
            ],
        ],
    ];
}

if (YesNo::isTrue(Registry::get('addons.social_buttons.vkontakte_enable'))) {
    $schema['services']['vkontakte'] = [
        'purposes' => ['functional'],
        'name' => 'vkontakte',
        'translations' => [
            'zz' => [
                'title' => 'social_buttons.vkontakte_cookie_title',
                'description' => 'social_buttons.vkontakte_cookie_description'
            ],
        ],
    ];
}

if (YesNo::isTrue(Registry::get('addons.social_buttons.yandex_enable'))) {
    $schema['services']['yandex'] = [
        'purposes' => ['functional'],
        'name' => 'yandex',
        'translations' => [
            'zz' => [
                'title' => 'social_buttons.yandex_cookie_title',
                'description' => 'social_buttons.yandex_cookie_description'
            ],
        ],
    ];
}

return $schema;
