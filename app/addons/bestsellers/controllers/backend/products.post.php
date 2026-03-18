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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode == 'manage') {
    if (!fn_bestsellers_is_eligible_to_edit_sales_amount($auth)) {
        return [CONTROLLER_STATUS_OK];
    }

    $selected_fields = Tygh::$app['view']->getTemplateVars('selected_fields');

    $selected_fields[] = array(
        'name' => '[data][sales_amount]',
        'text' => __('sales_amount')
    );

    Tygh::$app['view']->assign('selected_fields', $selected_fields);
} elseif ($mode == 'm_update') {
    if (!fn_bestsellers_is_eligible_to_edit_sales_amount($auth)) {
        return [CONTROLLER_STATUS_OK];
    }

    $selected_fields = Tygh::$app['session']['selected_fields'];

    $field_groups = Tygh::$app['view']->getTemplateVars('field_groups');
    $filled_groups = Tygh::$app['view']->getTemplateVars('filled_groups');
    $field_names = Tygh::$app['view']->getTemplateVars('field_names');

    if (!empty($selected_fields['data']['sales_amount'])) {
        $field_groups['B']['sales_amount'] = 'products_data';
        $filled_groups['B']['sales_amount'] = __('sales_amount');
    }

    if (isset($field_names['sales_amount'])) {
        unset($field_names['sales_amount']);
    }

    Tygh::$app['view']->assign('field_groups', $field_groups);
    Tygh::$app['view']->assign('filled_groups', $filled_groups);
    Tygh::$app['view']->assign('field_names', $field_names);
} elseif ($mode == 'update') {
    Tygh::$app['view']->assign('allow_edit_sales_amount', fn_bestsellers_is_eligible_to_edit_sales_amount($auth));
}
