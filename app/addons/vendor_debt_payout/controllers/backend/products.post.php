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

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    /** @var array<string, string> $product_data */
    $product_data = $view->getTemplateVars('product_data');
    if (
        empty($product_data['product_id'])
        || (int) $product_data['product_id'] !== fn_vendor_debt_payout_get_payout_product()
    ) {
        return [CONTROLLER_STATUS_OK];
    }

    $tabs = Registry::get('navigation.tabs');
    foreach ($tabs as $id => &$tab) {
        $tab['hidden'] = $id !== 'detailed';
    }
    unset($tab);
    Registry::set('navigation.tabs', $tabs);

    Tygh::$app['view']->assign('force_zero_company_id', true);
}

return [CONTROLLER_STATUS_OK];
