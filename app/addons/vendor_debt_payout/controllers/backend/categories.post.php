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
    /** @var array<string, string> $category_data */
    $category_data = $view->getTemplateVars('category_data');
    if (
        empty($category_data['category_id'])
        || (int) $category_data['category_id'] !== fn_vendor_debt_payout_get_payout_category()
    ) {
        return [CONTROLLER_STATUS_OK];
    }

    $tabs = Registry::get('navigation.tabs');
    foreach ($tabs as $id => &$tab) {
        $tab['hidden'] = $id !== 'detailed';
    }
    unset($tab);
    Registry::set('navigation.tabs', $tabs);
    $_REQUEST['show_block_manager'] = false;
}

return [CONTROLLER_STATUS_OK];
