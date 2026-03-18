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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'update_steps') {
    if (!empty($_REQUEST['arrival_terminal'])) {
        foreach($_REQUEST['arrival_terminal'] as $g_id => $terminal) {
            foreach($terminal as $s_id => $o_id) {
                $_SESSION['cart']['arrival_terminal'][$g_id][$s_id] = $o_id;
            }
        }
    }
}

if ($mode == 'checkout') {
    if (!empty($_REQUEST['arrival_terminal'])) {
        foreach($_REQUEST['arrival_terminal'] as $g_id => $terminal) {
            foreach($terminal as $s_id => $o_id) {
                $_SESSION['cart']['arrival_terminal'][$g_id][$s_id] = $o_id;
            }
        }
    }
}
