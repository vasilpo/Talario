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

use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode === 'complete') {
    $is_event = isset(Tygh::$app['session']['facebook_pixel']['order_placed']) ? Tygh::$app['session']['facebook_pixel']['order_placed'] : false;
    if ($is_event) {
        Tygh::$app['view']->assign('fb_track_order_placed_event', true);
        unset(Tygh::$app['session']['facebook_pixel']['order_placed']);

        if (!empty($_REQUEST['order_id'])) {
            $order_info = fn_get_order_info($_REQUEST['order_id']);

            Tygh::$app['view']->assign('fb_order_total', $order_info['total']);
        }
    }
}
