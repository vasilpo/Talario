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

if ($mode == 'list') {
    $params['status'] = 'A';
    $params['date'] = true;
    $params['full_info'] = true;
    $params['promotions'] = true;

    $chains = fn_buy_together_get_chains($params, $auth);

    if (!empty($chains)) {
        $promotions = Tygh::$app['view']->getTemplateVars('promotions');
        $promotions['chains'] = $chains;

        Tygh::$app['view']->assign('promotions', $promotions);
    }

}
