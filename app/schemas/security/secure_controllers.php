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

/**
 * Describes the behavior of controllers depending on whether secure connection is enabled or not.
 *
 * Syntax:
 * 'controller' => [
 *      'secure_mode' => 'active'/'passive'
 * ]
 *
 * secure_mode - value of the "Enable secure connection for the storefront" setting. Available values: none, full.
 * active - the controller can be processed only via HTTPS.
 * passive -  the controller can be processed both via HTTP and HTTPS.
 */

return [
    'payment_notification' => [
        'none' => 'passive',
    ],
    'image' => [
        'none' => 'passive',
    ],
    'robots' => [
        'none' => 'passive',
        'full' => 'passive'
    ],
];
