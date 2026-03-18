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
    return;
}

if ($mode == 'manage') {

    $selected_fields = Tygh::$app['view']->getTemplateVars('selected_fields');

    $selected_fields[] = array(
        'name' => '[data][is_returnable]',
        'text' => __('returnable')
    );

    $selected_fields[] = array(
        'name' => '[data][return_period]',
        'text' => __('return_period')
    );

    Tygh::$app['view']->assign('selected_fields', $selected_fields);

} elseif ($mode == 'm_update') {

    $selected_fields = Tygh::$app['session']['selected_fields'];

    $field_groups = Tygh::$app['view']->getTemplateVars('field_groups');
    $filled_groups = Tygh::$app['view']->getTemplateVars('filled_groups');
    $field_names = Tygh::$app['view']->getTemplateVars('field_names');

    if (!empty($selected_fields['data']['return_period'])) {
        $field_groups['B']['return_period'] = 'products_data';
        $filled_groups['B']['return_period'] = __('return_period');
    }

    if (!empty($selected_fields['data']['is_returnable'])) {
        $field_groups['C']['is_returnable'] = 'products_data';
        $filled_groups['C']['is_returnable'] = __('returnable_product');
    }

    if (isset($field_names['is_returnable'])) {
        unset($field_names['is_returnable']);
    }
    if (isset($field_names['return_period'])) {
        unset($field_names['return_period']);
    }

    Tygh::$app['view']->assign('field_groups', $field_groups);
    Tygh::$app['view']->assign('filled_groups', $filled_groups);
    Tygh::$app['view']->assign('field_names', $field_names);
}
