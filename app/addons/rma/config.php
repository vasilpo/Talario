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

define('STATUSES_RETURN', 'R');
define('ORDER_DATA_RETURN', 'H');
define('ORDER_DATA_PRODUCTS_DELIVERY_DATE', 'V');

define('RMA_REASON', 'R');
define('RMA_ACTION', 'A');
/** @deprecated since 4.10.4 use \Tygh\Enum\Addons\Rma\ReturnStatuses::REQUESTED  */
define('RMA_DEFAULT_STATUS', 'R');
/** @deprecated since 4.10.4 use \Tygh\Enum\Addons\Rma\ReturnStatuses::APPROVED */
define('RETURN_PRODUCT_ACCEPTED', 'A');
/** @deprecated since 4.10.4 use \Tygh\Enum\Addons\Rma\ReturnStatuses::DECLINED */
define('RETURN_PRODUCT_DECLINED', 'D');
