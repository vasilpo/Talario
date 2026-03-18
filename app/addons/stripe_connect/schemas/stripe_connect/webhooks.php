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

/*
 * Parameter 'enabled_events' is required in every webhook.
 */
return [
    'account.application.deauthorized' => [
        'connect'        => true,
        'enabled_events' => [
            'account.application.deauthorized'
        ],
    ],
    'payment_intent.succeeded'         => [
        'enabled_events' => [
            'payment_intent.succeeded'
        ],
    ],
    'payment_intent.canceled'          => [
        'enabled_events' => [
            'payment_intent.canceled'
        ],
    ],
];
