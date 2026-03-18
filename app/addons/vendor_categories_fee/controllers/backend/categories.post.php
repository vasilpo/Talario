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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode == 'update') {
    if (empty($_REQUEST['category_id'])) {
        return;
    }

    Registry::set('navigation.tabs.vendor_fee', [
        'title' => __('vendor_categories_fee.vendor_fee'),
        'js' => true,
    ]);

    $vendor_plans = fn_vendor_categories_fee_get_vendor_plans();
    $category_fee = fn_vendor_categories_fee_get_category_fee($_REQUEST['category_id']);
    $parent_fee = !fn_vendor_categories_fee_has_all_fee_set($category_fee)
        ? fn_vendor_categories_fee_get_parent_category_fee($_REQUEST['category_id'])
        : [];

    Tygh::$app['view']->assign([
        'vendor_plans' => $vendor_plans,
        'category_fee' => $category_fee,
        'parent_fee'   => $parent_fee,
    ]);
}
