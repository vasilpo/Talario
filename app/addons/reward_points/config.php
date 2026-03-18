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

//
// Defined variables
//

define('ORDER_DATA_POINTS_GAIN', 'A');

define('PRODUCT_REWARD_POINTS', 'P');
define('CATEGORY_REWARD_POINTS', 'C');
define('GLOBAL_REWARD_POINTS', 'A');

define('POINTS', 'W');
define('POINTS_MODIFIER_TYPE', 'R');
define('POINTS_IN_USE', 'I');

//
//These constants define the reason for the change of points
//
define('CHANGE_DUE_ORDER', 'O');
define('CHANGE_DUE_USE', 'I');
define('CHANGE_DUE_RMA', 'R');
define('CHANGE_DUE_ADDITION', 'A');
define('CHANGE_DUE_SUBTRACT', 'S');
define('CHANGE_DUE_ORDER_DELETE', 'D');
define('CHANGE_DUE_ORDER_PLACE', 'P');
