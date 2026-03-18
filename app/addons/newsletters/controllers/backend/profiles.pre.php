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

if ($mode == 'picker' && !empty($_REQUEST['picker_for']) && $_REQUEST['picker_for'] == 'subscribers') {
    $mailing_lists = db_get_hash_single_array("SELECT m.list_id, d.object FROM ?:mailing_lists AS m INNER JOIN ?:common_descriptions AS d ON m.list_id=d.object_id WHERE d.object_holder='mailing_lists' AND d.lang_code = ?s", array('list_id', 'object'), DESCR_SL);

    Tygh::$app['view']->assign('mailing_lists', $mailing_lists);
}
