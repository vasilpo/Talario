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

/** @var array $schema */
$schema['/payment_notification/result/robokassa'] = [
    'dispatch' => 'payment_notification.result',
    'payment'  => 'robokassa',
];

$schema['/payment_notification/success/robokassa'] = [
    'dispatch' => 'payment_notification.return',
    'payment'  => 'robokassa',
];

$schema['/payment_notification/fail/robokassa'] = [
    'dispatch' => 'payment_notification.cancel',
    'payment'  => 'robokassa',
];

return $schema;
