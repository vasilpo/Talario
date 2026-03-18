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

if ($mode === 'details' && Registry::ifGet('config.discussion.enable_order_communication', false)) {
    if (!empty($_REQUEST['order_id'])) {
        if ($discussion = fn_get_discussion($_REQUEST['order_id'], 'O', true, $_REQUEST)) {
            if ($discussion['type'] != 'D') {
                $navigation_tabs = Registry::get('navigation.tabs');
                $navigation_tabs['discussion'] = array(
                    'title' => __('communication'),
                    'js' => true
                );

                Registry::set('navigation.tabs', $navigation_tabs);

                Tygh::$app['view']->assign('discussion', $discussion);
            }
        }
    }
}
