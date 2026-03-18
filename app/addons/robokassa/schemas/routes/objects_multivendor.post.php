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
$schema['/payment_notification/result/robokassa_split'] = [
    'dispatch' => 'payment_notification.result',
    'payment'  => 'robokassa_split',
];

$schema['/payment_notification/success/robokassa_split'] = [
    'dispatch' => 'payment_notification.success',
    'payment'  => 'robokassa_split',
];

$schema['/payment_notification/fail/robokassa_split'] = [
    'dispatch' => 'payment_notification.fail',
    'payment'  => 'robokassa_split',
];

return $schema;
