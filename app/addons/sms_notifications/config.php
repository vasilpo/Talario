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

define('SMS_NOTIFICATIONS_SMS_LENGTH_UNICODE', 70);
define('SMS_NOTIFICATIONS_SMS_LENGTH', 159); // usually 160, but the euro sign and some others will be coded as 2 characters.
define('SMS_NOTIFICATIONS_SMS_LENGTH_CONCAT', 7); // If a message is concatenated, it reduces the number of characters contained in each message by 7
define('SMS_NOTIFICATIONS_API_URL', 'https://platform.clickatell.com/messages/http/send');
