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

/** @var string $mode */
if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && $mode === 'update'
    && $_REQUEST['addon'] === 'vendor_debt_payout'
) {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    $view->assign([
        'addon_setting_ids'  => fn_vendor_dept_payout_get_template_setting_ids(),
        'payout_product_id'  => fn_vendor_debt_payout_get_payout_product(),
        'payout_category_id' => fn_vendor_debt_payout_get_payout_category(),
    ]);
}

return [CONTROLLER_STATUS_OK];
