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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

// max ammount of products in order to pass separate entries to paypal
fn_define('MAX_PAYPAL_PRODUCTS', 100);
// max product description length to pass to paypal
fn_define('MAX_PAYPAL_DESCR_LENGTH', 126);
// paypal's IPN identifier for refunded transactions
fn_define('PAYPAL_ORDER_STATUS_REFUNDED', 'Refunded');
// paypal's IPN identifier for completed transactions
fn_define('PAYPAL_ORDER_STATUS_COMPLETED', 'Completed');
// ingore partial refund policy identifier (see Order status on partial refund addon setting)
fn_define('PAYPAL_PARTIAL_REFUND_IGNORE', 'ignore');