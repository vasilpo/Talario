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

$notice_addons = [
    'seo',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_REQUEST['addon']) && in_array($_REQUEST['addon'], $notice_addons) && in_array($mode, ['update', 'install', 'uninstall'])) {
        fn_se_display_addon_notice($_REQUEST['addon']);
    }

    return;
}

if ($mode == 'update') {
    if ($_REQUEST['addon'] == 'searchanise') {
        fn_se_check_connect();
        fn_se_check_queue();
    }

} elseif ($mode == 'update_status' && in_array($_REQUEST['id'], $notice_addons)) {
    fn_se_display_addon_notice($_REQUEST['addon']);
}
