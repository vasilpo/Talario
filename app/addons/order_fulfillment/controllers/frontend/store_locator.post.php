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

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'search' && !empty($_REQUEST['sl_search'])) {
    $view = Tygh::$app['view'];
    $group_locations = $view->getTemplateVars('store_locations');
    /** @psalm-suppress InvalidArrayOffset */
    if (isset($group_locations['0'])) {
        $marketplace_name = __('marketplace');
        $group_locations[$marketplace_name] = $group_locations['0'];
        unset($group_locations['0']);
        $view->assign('store_locations', $group_locations);
    }
}
