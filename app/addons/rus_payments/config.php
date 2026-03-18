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

define('PAYLER_TIMEOUT', 45);

// Time for awaiting callback
define('RBK_MAX_AWAITING_TIME', 20);
define('RK_MAX_AWAITING_TIME', 10);
define('YM_MAX_AWAITING_TIME', 10);

define('PAYMASTER_MAX_AWAITING_TIME', 10);
define('PAYANYWAY_GATEWAY_URL', 'https://kassa.payanyway.ru');

fn_define('YANDEX_MONEY_CODE_SUCCESS', 0);
fn_define('YANDEX_MONEY_CODE_AUTH_ERROR', 1);
fn_define('YANDEX_MONEY_CODE_TRANSFER_REFUSED', 100);
fn_define('YANDEX_MONEY_CODE_REQUEST_PARSE_ERROR', 200);
