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
 * @var array<string, array> $schema
 */
$schema['/yoomoney/check_payment'] = [
    'dispatch' => 'yandex_checkout.check_payment',
];
$schema['/yoomoney/return_to_store/[i:order_id]'] = [
    'dispatch' => 'yandex_checkout.return_to_store',
];
$schema['/yoomoney/platform/return_to_store/[i:order_id]'] = [
    'dispatch' => 'yandex_checkout_for_marketplaces.return_to_store',
];
return $schema;
