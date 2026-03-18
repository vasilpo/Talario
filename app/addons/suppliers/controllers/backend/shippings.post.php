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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode == 'update') {
    Registry::set('navigation.tabs.suppliers', array (
        'title' => __('suppliers'),
        'js' => true
    ));

    $shipping_data = Tygh::$app['view']->getTemplateVars('shipping');

    list($suppliers) = fn_get_suppliers();
    if (fn_allowed_for('ULTIMATE') && !fn_get_runtime_company_id()) {
        $suppliers = fn_suppliers_filter_objects_by_sharing(
            $suppliers,
            'suppliers',
            'supplier_id',
            'shippings',
            $shipping_data['shipping_id']
        );
    }

    $linked_suppliers = fn_get_shippings_suppliers($shipping_data['shipping_id']);

    Tygh::$app['view']->assign('suppliers', $suppliers);
    Tygh::$app['view']->assign('linked_suppliers', $linked_suppliers);
}
