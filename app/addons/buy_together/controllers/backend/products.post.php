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

if ($mode == 'update') {
    $is_restricted = false;
    $show_notice = false;

    fn_set_hook('buy_together_restricted_product', $_REQUEST['product_id'], $auth, $is_restricted, $show_notice);

    if (!$is_restricted) {
        Registry::set('navigation.tabs.buy_together', array (
            'title' => __('buy_together'),
            'js' => true
        ));

        $params = array(
            'product_id' => $_REQUEST['product_id'],
        );

        $chains = fn_buy_together_get_chains($params, array(), DESCR_SL);

        Tygh::$app['view']->assign('chains', $chains);
    }
}
